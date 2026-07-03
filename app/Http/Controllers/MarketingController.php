<?php

namespace App\Http\Controllers;

use App\Http\Traits\ActiveProjectTrait;
use App\Models\Project;
use App\Models\AuditLog;
use App\Models\Notification;
use App\Http\Requests\GenerateSocialSuiteRequest;
use App\Http\Requests\GenerateGrowthSuiteRequest;
use App\Http\Requests\GenerateAdCampaignRequest;
use App\Http\Requests\GenerateMarketingPostRequest;
use App\Http\Requests\LaunchMarketingCampaignRequest;
use App\Services\GeminiService;
use App\Services\ContentGenerationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MarketingController extends Controller
{
    use ActiveProjectTrait;

    protected \App\Services\CreditService $creditService;
    protected \App\Services\ContentModerationService $moderationService;

    public function __construct(
        \App\Services\CreditService $creditService,
        \App\Services\ContentModerationService $moderationService
    ) {
        $this->creditService = $creditService;
        $this->moderationService = $moderationService;
    }

    protected function checkClientCredits(int $userId, int $required = 1)
    {
        $res = $this->creditService->checkClientCredits($userId, $required);
        if ($res) {
            return response()->json([
                'success' => false,
                'error' => $res['error']
            ], $res['code']);
        }
        return null;
    }

    protected function checkInputForNsfw(array $inputs, int $userId, string $action)
    {
        $res = $this->moderationService->checkInputs($inputs, $userId, $action, request()->ip());
        if ($res) {
            return response()->json($res, 422);
        }
        return null;
    }

    protected function deductCredits(int $userId, int $amount, string $action, ?string $details = null)
    {
        return $this->creditService->deductCredits($userId, $amount, $action, $details);
    }

    public function marketing()
    {
        return view('marketing');
    }

    public function generateSocialSuite(GenerateSocialSuiteRequest $request)
    {
        $userId = $this->getAuthUserId();
        $user = \App\Models\User::find($userId);

        $creditCheck = $this->checkClientCredits($userId, 1);
        if ($creditCheck) {
            return $creditCheck;
        }

        $nsfwBlock = $this->checkInputForNsfw($request->all(), $userId, 'AI Content Generation');
        if ($nsfwBlock) {
            return $nsfwBlock;
        }

        $prompt = "You are a world-class digital marketer and copywriter.
Generate high-converting, tailored social media marketing posts for the following business:
Business Description: {$request->business_description}
Tone of Voice: {$request->tone}
Target Audience: {$request->target_audience}
Call to Action / Offers: " . ($request->cta ?: 'None specified') . "

Generate tailored posts for each of the following platforms:
1. linkedin: Professional, value-driven, structured, 2-3 hashtags.
2. twitter: Catchy, concise, single tweet style, max 280 characters, 1-2 hashtags.
3. telegram: Direct, engaging, bullet-point format, call to action.
4. facebook: Engaging, community-focused, welcoming, including CTA and emojis.
5. reddit: Value-first educational post, avoiding salesy tone, focus on solving a problem, no hashtags.

Also generate:
6. image_prompt: A short, punchy 5-10 word prompt for an image generator (no punctuation) describing a background or graphic matching the campaign theme.

Output must be in JSON format matching the schema.";

        $schema = [
            'type' => 'OBJECT',
            'properties' => [
                'linkedin' => ['type' => 'STRING'],
                'twitter' => ['type' => 'STRING'],
                'telegram' => ['type' => 'STRING'],
                'facebook' => ['type' => 'STRING'],
                'reddit' => ['type' => 'STRING'],
                'image_prompt' => ['type' => 'STRING']
            ],
            'required' => ['linkedin', 'twitter', 'telegram', 'facebook', 'reddit', 'image_prompt']
        ];

        try {
            $data = $this->callGeminiWithRotation($prompt, $schema);

            if ($user && $user->role === 'client') {
                $this->deductCredits($userId, 1, 'AI Content Generation', 'Generated Social Media Suite');
            }

            return response()->json([
                'success' => true,
                'posts' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Gemini API Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function generateGrowthSuite(GenerateGrowthSuiteRequest $request)
    {
        $userId = $this->getAuthUserId();
        $user = \App\Models\User::find($userId);

        $creditCheck = $this->checkClientCredits($userId, 1);
        if ($creditCheck) {
            return $creditCheck;
        }

        $nsfwBlock = $this->checkInputForNsfw($request->all(), $userId, 'AI Content Generation');
        if ($nsfwBlock) {
            return $nsfwBlock;
        }

        $prompt = "You are a world-class digital marketer, SEO expert, and content strategist.
Generate a weekly growth content pack based on:
Business Description: {$request->business_description}
Target Audience: {$request->target_audience}
Campaign/Growth Goal: {$request->campaign_goal}

Generate the following components:
1. blog_outline: A comprehensive outline for an educational blog post targeting search queries (including catchy title, introduction synopsis, 3 main subheadings with bullet points, and conclusion/CTA).
2. newsletter_copy: An engaging, personal, and high-converting email newsletter to build trust and drive conversions (subject line, opening hook, body with value add, and clear Call to Action).
3. content_calendar: A structured list of content topics and formats for each day of the week (Monday through Sunday, e.g., 'Monday: X thread on Y topic', 'Tuesday: LinkedIn post on Z', etc.) designed to achieve the growth goal.

Output must be in JSON format matching the schema.";

        $schema = [
            'type' => 'OBJECT',
            'properties' => [
                'blog_outline' => ['type' => 'STRING'],
                'newsletter_copy' => ['type' => 'STRING'],
                'content_calendar' => [
                    'type' => 'ARRAY',
                    'items' => ['type' => 'STRING']
                ]
            ],
            'required' => ['blog_outline', 'newsletter_copy', 'content_calendar']
        ];

        try {
            $data = $this->callGeminiWithRotation($prompt, $schema);

            if ($user && $user->role === 'client') {
                $this->deductCredits($userId, 1, 'AI Content Generation', 'Generated Weekly Growth Pack');
            }

            return response()->json([
                'success' => true,
                'growth_pack' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Gemini API Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function generateAdCampaign(GenerateAdCampaignRequest $request)
    {
        $userId = $this->getAuthUserId();
        $user = \App\Models\User::find($userId);

        $creditCheck = $this->checkClientCredits($userId, 1);
        if ($creditCheck) {
            return $creditCheck;
        }

        $nsfwBlock = $this->checkInputForNsfw($request->all(), $userId, 'AI Content Generation');
        if ($nsfwBlock) {
            return $nsfwBlock;
        }

        $prompt = "You are a world-class digital ad campaign specialist (Google Ads, Meta Ads).
Build an AI Campaign suggestion package for:
Product/Service: {$request->product}
Target Audience: {$request->target_audience}
Budget/Constraints: {$request->budget}
Campaign Goal: {$request->campaign_goal}

Generate the following digital advertising assets:
1. target_audience: Specifically detailed personas and demographic targeting recommendations for Meta and Google.
2. keywords: 10 highly relevant search keywords/phrases (divided into high-intent, competitor-related, and broad-match categories).
3. headlines: 5 catchy headlines (max 30 characters each for Google Search Ads style).
4. ad_copy: 3 variations of engaging ad copy/descriptions (Google/Meta style, with hooks and CTA).
5. creative_ideas: 3 visual or storyboard concepts for ad creatives (e.g. image prompts or video hooks).
6. landing_page: Structure, core value proposition headline, key benefits to display, and call-to-action details for a high-converting landing page.

Output must be in JSON format matching the schema.";

        $schema = [
            'type' => 'OBJECT',
            'properties' => [
                'target_audience' => ['type' => 'STRING'],
                'keywords' => [
                    'type' => 'ARRAY',
                    'items' => ['type' => 'STRING']
                ],
                'headlines' => [
                    'type' => 'ARRAY',
                    'items' => ['type' => 'STRING']
                ],
                'ad_copy' => [
                    'type' => 'ARRAY',
                    'items' => ['type' => 'STRING']
                ],
                'creative_ideas' => [
                    'type' => 'ARRAY',
                    'items' => ['type' => 'STRING']
                ],
                'landing_page' => ['type' => 'STRING']
            ],
            'required' => ['target_audience', 'keywords', 'headlines', 'ad_copy', 'creative_ideas', 'landing_page']
        ];

        try {
            $data = $this->callGeminiWithRotation($prompt, $schema);

            if ($user && $user->role === 'client') {
                $this->deductCredits($userId, 1, 'AI Content Generation', 'Generated Ad Campaign Hub');
            }

            return response()->json([
                'success' => true,
                'campaign' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Gemini API Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function generateMarketingPost(GenerateMarketingPostRequest $request)
    {
        $userId = $this->getAuthUserId();
        $user = \App\Models\User::find($userId);

        $creditCheck = $this->checkClientCredits($userId, 1);
        if ($creditCheck) {
            return $creditCheck;
        }

        $nsfwBlock = $this->checkInputForNsfw($request->all(), $userId, 'AI Content Generation');
        if ($nsfwBlock) {
            return $nsfwBlock;
        }

        $prompt = "You are a world-class digital marketer and copywriter.
Generate a high-converting social media marketing post for the following business:
Business Description: {$request->business_description}
Target Platform: {$request->platform}
Tone of Voice: {$request->tone}
Target Audience: {$request->target_audience}
Call to Action / Offers: " . ($request->cta ?: 'None specified') . "

Generate the following fields:
1. Title: A catchy hook or headline for the post (max 10 words).
2. Description: The main body text/copy of the post (optimized for {$request->platform}, including appropriate hashtags and formatting).
3. Image Description: A short, punchy 5-10 word prompt for an image generator (no punctuation, e.g. 'wood fired organic sourdough bread bakery').

Output must be in JSON format matching the schema.";

        $schema = [
            'type' => 'OBJECT',
            'properties' => [
                'title' => ['type' => 'STRING'],
                'description' => ['type' => 'STRING'],
                'image_prompt' => ['type' => 'STRING']
            ],
            'required' => ['title', 'description', 'image_prompt']
        ];

        try {
            $data = $this->callGeminiWithRotation($prompt, $schema);

            if ($user && $user->role === 'client') {
                $this->deductCredits($userId, 1, 'AI Content Generation', 'Generated Marketing Post (' . $request->platform . ')');
            }

            return response()->json([
                'success' => true,
                'title' => $data['title'],
                'description' => $data['description'],
                'image_prompt' => $data['image_prompt']
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Gemini API Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function launchMarketingCampaign(LaunchMarketingCampaignRequest $request)
    {
        $projectId = $this->getActiveProjectId();
        $project = Project::find($projectId);
        $projectName = $project ? $project->name : 'Active Project';

        // Save campaign creative in database with generating state
        $post = \App\Models\Post::create([
            'project_id' => $projectId,
            'platform' => $request->platform,
            'external_id' => 'launch_' . uniqid(),
            'title' => $request->title,
            'content' => $request->description,
            'author' => 'Lakshya Admin',
            'url' => $project->url ?? 'https://lakshya.ai',
            'status' => 'Launched',
            'image_prompt' => $request->image_prompt,
            'image_url' => 'generating'
        ]);

        // Submit the image generation task to the Python worker queue API
        try {
            $vmConfig = config('admin.vm');
            Http::timeout($vmConfig['timeout'])->post($vmConfig['base_url'] . '/queue-task', [
                'client_id' => $this->getAuthUserId(),
                'task_type' => 'generate_image',
                'payload' => [
                    'prompt' => $request->image_prompt,
                    'post_id' => $post->id
                ]
            ]);
        } catch (\Exception $e) {
            \Log::warning("Failed to queue admin campaign image generation task for post ID {$post->id}: " . $e->getMessage());
        }

        // Log campaign in AuditLog
        AuditLog::create([
            'user_id' => $this->getAuthUserId(),
            'action' => "Launched {$request->platform} digital marketing campaign '{$request->title}' for project '{$projectName}'",
            'target_table' => 'campaigns',
            'ip_address' => $request->ip()
        ]);

        // Create notification
        Notification::create([
            'user_id' => $this->getAuthUserId(),
            'title' => 'Campaign Launched! 🚀',
            'message' => "Campaign '{$request->title}' has been successfully published to {$request->platform}.",
            'is_read' => false
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Campaign successfully launched!'
        ]);
    }

    /**
     * Call Gemini API with key rotation, or use mock data as fallback.
     */
    protected function callGeminiWithRotation(string $prompt, array $schema): array
    {
        $gemini = app(GeminiService::class);
        
        if ($gemini->hasKeys()) {
            return $gemini->generateContent($prompt, $schema);
        }
        
        // Fallback to mock data when no API keys configured
        $mockGenerator = app(ContentGenerationService::class);
        return $mockGenerator->generateMockDataForSchema($schema, $prompt);
    }
}
