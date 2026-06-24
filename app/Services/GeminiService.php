<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    /**
     * The Gemini model to use.
     */
    protected string $model;

    /**
     * API keys for round-robin rotation.
     */
    protected array $keys;

    /**
     * Maximum timeout for API calls in seconds.
     */
    protected int $timeout;

    public function __construct()
    {
        $keysString = config('services.gemini.keys') ?? config('services.gemini.key');
        $this->keys = array_filter(array_map('trim', explode(',', $keysString ?: '')));
        $this->model = config('services.gemini.model', 'gemini-2.5-flash');
        $this->timeout = (int) config('services.gemini.timeout', 60);
    }

    /**
     * Check if API keys are configured.
     */
    public function hasKeys(): bool
    {
        return !empty($this->keys);
    }

    /**
     * Call the Gemini API with key rotation and retry logic.
     *
     * @param string $prompt
     * @param array $schema
     * @return array
     * @throws \Exception
     */
    public function generateContent(string $prompt, array $schema): array
    {
        if (empty($this->keys)) {
            throw new \Exception('Gemini API Key is not configured. Set GEMINI_API_KEY in your .env file.');
        }

        $totalKeys = count($this->keys);
        $currentIndex = cache()->get('gemini_key_index', 0) % $totalKeys;

        for ($attempt = 0; $attempt < $totalKeys; $attempt++) {
            $keyIndex = ($currentIndex + $attempt) % $totalKeys;
            $apiKey = $this->keys[$keyIndex];

            try {
                $response = Http::timeout($this->timeout)
                    ->retry(2, 2000)
                    ->withHeaders(['Content-Type' => 'application/json'])
                    ->post(
                        "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key={$apiKey}",
                        [
                            'contents' => [
                                ['parts' => [['text' => $prompt]]],
                            ],
                            'generationConfig' => [
                                'responseMimeType' => 'application/json',
                                'responseSchema' => $schema,
                            ],
                        ]
                    );

                if ($response->successful()) {
                    cache()->put('gemini_key_index', ($keyIndex + 1) % $totalKeys, 86400);

                    $result = $response->json();
                    $text = $result['candidates'][0]['content']['parts'][0]['text'] ?? '';
                    $data = json_decode($text, true);

                    if ($data !== null) {
                        return $data;
                    }
                }

                Log::warning("Gemini API key index {$keyIndex} failed with status {$response->status()}. Retrying...");
            } catch (\Exception $e) {
                Log::warning("Gemini API key index {$keyIndex} exception: {$e->getMessage()}. Retrying...");
            }
        }

        throw new \Exception('All configured Gemini API keys returned errors or rate limits.');
    }
}