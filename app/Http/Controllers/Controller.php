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

            // Intercept fake/mock keys for local development and Playwright browser checks
            if (str_starts_with($apiKey, 'AQ.') || str_starts_with($apiKey, 'mock')) {
                \Illuminate\Support\Facades\Log::info("Mocking Gemini response for key index {$keyIndex}");
                return $this->mockResponseForSchema($schema, $prompt);
            }

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

    /**
     * Build standard mock objects based on the requested JSON response schema.
     */
    protected function mockResponseForSchema(array $schema, $prompt = '')
    {
        if (($schema['type'] ?? '') === 'OBJECT' && isset($schema['properties'])) {
            $data = [];
            foreach ($schema['properties'] as $key => $prop) {
                $data[$key] = $this->mockValueForProperty($key, $prop);
            }
            return $data;
        }
        return [];
    }

    private function mockValueForProperty($name, $prop)
    {
        $type = $prop['type'] ?? 'STRING';
        if ($type === 'ARRAY') {
            if ($name === 'content_calendar') {
                return [
                    "Monday: Share introductory post on LinkedIn about WhatsApp automation.",
                    "Tuesday: Tweet about saving hours of manual sales follow-up using email outreach.",
                    "Wednesday: Post a value-first guide on Reddit discussing visitor intent scoring.",
                    "Thursday: Send weekly growth newsletter detailing product demo outcomes.",
                    "Friday: Launch digital ad campaign targeting SaaS startup decision makers.",
                    "Saturday: Share a client success story on Facebook page.",
                    "Sunday: Perform target list review and planning session."
                ];
            }
            if ($name === 'keywords') {
                return [
                    "high intent lead generation",
                    "automated client onboarding",
                    "b2b email marketing tools",
                    "whatsapp sales CRM"
                ];
            }
            if ($name === 'headlines') {
                return [
                    "Automate Your CRM Today",
                    "AI Cold Outreach System",
                    "10x Lead Conversion Rate",
                    "Smart Visitor Tracking",
                    "Free 14 Day Trial Setup"
                ];
            }
            if ($name === 'ad_copy') {
                return [
                    "Stop losing 98% of your website traffic. Our AI website visitor tracker identifies high intent leads instantly.",
                    "Automate your sales pipeline from discovery to booking. Let our AI Agent run email sequences on autopilot.",
                    "Boost your conversion rate and close premium clients without manual outbound effort. Get started free."
                ];
            }
            if ($name === 'creative_ideas') {
                return [
                    "A clean screenshot showing the CRM target board with visual drag and drop columns.",
                    "A vibrant graphic illustration of an AI agent sending outbound emails in real time.",
                    "A comparison chart demonstrating conversion rates before and after implementing smart tracking."
                ];
            }
            return ["Mock item 1 for $name", "Mock item 2 for $name"];
        }

        // It is a string
        switch ($name) {
            case 'linkedin':
                return "[Mock] LinkedIn: Are you tired of manual B2B lead hunting? 🤖\n\nLakshya.ai automates your entire pipeline - tracking site visitors, scoring intent, and writing personalized follow-ups. Stop spending hours writing emails. Let AI do the legwork.\n\n👉 Try it today: https://lakshya.ai\n\n#SaaS #LeadGeneration #OutreachAutomation";
            case 'twitter':
                return "[Mock] Twitter: Stop wasting time on manual outbound! 🚀 Lakshya.ai identifies warm B2B leads visiting your site and writes tailored emails instantly. Try it free: https://lakshya.ai #SaaS #Outreach";
            case 'telegram':
                return "[Mock] Telegram:\n\n🔥 **Automate Your B2B Outreach** 🔥\n\nLakshya.ai tracks site visitors, scores their buying intent, and automatically initiates cold email and WhatsApp campaigns.\n\nGet a 14-day free trial:\n👉 https://lakshya.ai/consult";
            case 'facebook':
                return "[Mock] Facebook: Turn anonymous website visitors into qualified sales leads automatically! 📈 With Lakshya.ai, you can track visitor intent, compile lead profiles, and launch automated outreach via Gmail/Outlook. Start your free trial today: https://lakshya.ai";
            case 'reddit':
                return "[Mock] Reddit: How we automated our outreach process using customized email sequences. No sales pitch, just value. By identifying anonymous website traffic and automatically writing outreach emails based on visited pages, we boosted conversions by 40%.";
            case 'image_prompt':
                return "minimalist dashboard visual showing lead conversion graph";
            case 'blog_outline':
                return "[Mock Blog Outline]\nTitle: 5 Ways to Automate B2B Outbound Campaigns\n1. Introduction\n2. The Power of Intent Scoring\n3. Dynamic Email Personalization\n4. Multichannel Outreach (WhatsApp & Email)\n5. Conclusion and Call to Action";
            case 'newsletter_copy':
                return "[Mock Newsletter]\nSubject: Build recurring revenue automatically\n\nHi SaaS Founder,\n\nDid you know that 98% of visitors leave your site without leaving an email? Lakshya.ai fixes that. We track company details, score buying intent, and trigger outreach immediately.\n\nBest,\nThe Lakshya Team";
            case 'target_audience':
                return "B2B SaaS Founders, VP of Sales, and digital marketing managers seeking automated lead generation solutions.";
            case 'landing_page':
                return "[Mock Landing Page Blueprint]\n- Headline: Automate Your Lead Acquisition in Minutes\n- Subheadline: Track anonymous visitors, score intent, and initiate outbound outreach automatically.\n- Key Benefits: Multi-channel outreach, smart intent scoring, no-code CRM integration.\n- Primary Call to Action: Start Free 14-Day Trial";
            case 'title':
                return "[Mock] Next-Gen Marketing Automation";
            case 'description':
                return "[Mock] Boost your conversions with Lakshya's multi-channel AI outbound agent. Automated follow-ups, intent tracking, and email warmup included! #SaaS";
            default:
                return "[Mock] Content generated for $name.";
        }
    }
}
