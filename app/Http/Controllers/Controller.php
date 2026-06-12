<?php

namespace App\Http\Controllers;

abstract class Controller
{
    /**
     * Call the Gemini API with key rotation and retry logic.
     *
     * @param string $prompt
     * @param array $schema
     * @return array
     * @throws \Exception
     */
    protected function callGeminiWithRotation($prompt, array $schema)
    {
        $keysString = env('GEMINI_API_KEYS') ?? env('GEMINI_API_KEY');
        if (!$keysString) {
            throw new \Exception('Gemini API Key is not configured in the environment.');
        }

        // Split keys and sanitize them
        $keys = array_filter(array_map('trim', explode(',', $keysString)));
        if (empty($keys)) {
            throw new \Exception('No valid Gemini API keys found in configuration.');
        }

        $totalKeys = count($keys);
        
        // Retrieve last key index used from cache to distribute requests evenly
        $currentIndex = cache()->get('gemini_key_index', 0) % $totalKeys;

        // Cycle through all keys starting at the cached index
        for ($attempt = 0; $attempt < $totalKeys; $attempt++) {
            $keyIndex = ($currentIndex + $attempt) % $totalKeys;
            $apiKey = $keys[$keyIndex];

            try {
                $response = \Illuminate\Support\Facades\Http::retry(3, 1500)
                    ->withoutVerifying()
                    ->withHeaders(['Content-Type' => 'application/json'])
                    ->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}", [
                        'contents' => [
                            [
                                'parts' => [
                                    ['text' => $prompt]
                                ]
                            ]
                        ],
                        'generationConfig' => [
                            'responseMimeType' => 'application/json',
                            'responseSchema' => $schema
                        ]
                    ]);

                if ($response->successful()) {
                    // Update cache for the next request cycle
                    cache()->put('gemini_key_index', ($keyIndex + 1) % $totalKeys, 86400);

                    $result = $response->json();
                    $text = $result['candidates'][0]['content']['parts'][0]['text'] ?? '';
                    $data = json_decode($text, true);

                    if ($data) {
                        return $data;
                    }
                }

                // Log failure to retry with next key
                \Illuminate\Support\Facades\Log::warning("Gemini API key index {$keyIndex} failed with status {$response->status()}. Retrying with next key...");

            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning("Gemini API key index {$keyIndex} encountered exception: {$e->getMessage()}. Retrying with next key...");
            }
        }

        throw new \Exception('All configured Gemini API keys returned errors or rate limits.');
    }
}
