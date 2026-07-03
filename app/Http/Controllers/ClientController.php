<?php

namespace App\Http\Controllers;

use App\Http\Traits\ActiveProjectTrait;
use App\Models\User;
use App\Models\Project;
use App\Models\AuditLog;
use App\Models\Notification;
use App\Services\GeminiService;
use App\Services\ContentGenerationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class ClientController extends Controller
{
    use ActiveProjectTrait;

    /**
     * Helper to get the active client context.
     * If impersonating, returns the impersonated user, else falls back to auth user.
     */
    private function getClientUser()
    {
        if (session()->has('impersonating_client_id')) {
            return User::find(session('impersonating_client_id'));
        }
        // Fallback for testing: return the first seeded client if not logged in
        return User::where('role', 'client')->first();
    }

    // --- Admin Client Directory ---
    public function adminIndex()
    {
        $clients = User::where('role', 'client')->with(['subscription', 'projects'])->get();

        // Calculate and attach mock analytics on the fly
        foreach ($clients as $client) {
            $stats = $this->getClientMockStats($client);
            $client->total_reach = $stats['reach'];
            $client->total_clicks = $stats['clicks'];
            $client->total_spend = $stats['spend'];
            $client->total_commission = $stats['commission'];
        }

        return view('admin.clients', compact('clients'));
    }

    // --- Impersonation controls ---
    public function impersonateClient($id)
    {
        $client = User::findOrFail($id);
        
        session(['impersonating_client_id' => $client->id]);

        // Audit Log
        AuditLog::create([
            'user_id' => null, // Admin action
            'action' => "Initiated client simulation/testing mode for client '{$client->name}'",
            'target_table' => 'users',
            'ip_address' => request()->ip()
        ]);

        return redirect()->route('client.dashboard')->with('info', "Simulation mode active for client: {$client->name}. You are now viewing client-specific dashboards.");
    }

    public function exitImpersonate()
    {
        session()->forget('impersonating_client_id');
        session()->forget('active_project_id');

        return redirect()->route('admin.clients')->with('info', "Simulation mode deactivated. Returned to Admin dashboard.");
    }

    // --- Client Dashboard ---
    public function dashboard()
    {
        $client = $this->getClientUser();
        if (!$client) {
            return redirect()->route('admin.clients')->with('info', 'No active client session found.');
        }

        $stats = $this->getClientMockStats($client);
        $project = Project::where('user_id', $client->id)->first() ?? new Project();

        // Build 7-day trend statistics for charts
        $days = [];
        $reachTrend = [];
        $clickTrend = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $days[] = $date->format('M d');
            
            // Generate stable pseudorandom curve based on user id and date index
            $seed = ($client->id * 10) + $i;
            $reachTrend[] = round($stats['reach'] / 7 + sin($seed) * ($stats['reach'] / 20));
            $clickTrend[] = round($stats['clicks'] / 7 + cos($seed) * ($stats['clicks'] / 25));
        }

        // Fetch launched campaign posts for this client
        $campaigns = \App\Models\Post::where('project_id', $project->id)
            ->where('status', 'Launched')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('clients.dashboard', compact('client', 'stats', 'project', 'days', 'reachTrend', 'clickTrend', 'campaigns'));
    }

    // --- Client Creative Builder ---
    public function marketing()
    {
        $client = $this->getClientUser();
        $project = Project::where('user_id', $client->id)->first();
        
        return view('clients.marketing', compact('client', 'project'));
    }

    public function generateCampaign(Request $request)
    {
        $request->validate([
            'business_description' => 'required|string',
            'platform' => 'required|string',
            'tone' => 'required|string',
            'target_audience' => 'required|string',
            'cta' => 'nullable|string',
        ]);

        $prompt = "You are a world-class digital marketer and copywriter.
Generate a high-converting social media marketing post with affiliate links for the following business:
Business Description: {$request->business_description}
Target Platform: {$request->platform}
Tone of Voice: {$request->tone}
Target Audience: {$request->target_audience}
Call to Action / Affiliate Offer: " . ($request->cta ?: 'None specified') . "

Generate the following fields:
1. Title: A catchy hook or headline for the post (max 10 words).
2. Description: The main body text/copy of the post (optimized for {$request->platform}, incorporating affiliate marketing links and appropriate hashtags/formatting).
3. Image Description: A short, punchy 5-10 word prompt for an image generator (no punctuation, e.g. 'sterling silver botanical rings on mossy background').

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

    public function launchCampaign(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'platform' => 'required|string',
            'image_prompt' => 'required|string',
        ]);

        $client = $this->getClientUser();
        $project = Project::where('user_id', $client->id)->first();
        if (!$project) {
            return response()->json(['success' => false, 'error' => 'No active campaign project found.'], 404);
        }

        // Save campaign creative in database with generating state
        $post = \App\Models\Post::create([
            'project_id' => $project->id,
            'platform' => $request->platform,
            'external_id' => 'launch_' . uniqid(),
            'title' => $request->title,
            'content' => $request->description,
            'author' => $client->name,
            'url' => $project->url ?? 'http://amzn.to/example-affiliate-link',
            'status' => 'Launched',
            'image_prompt' => $request->image_prompt,
            'image_url' => 'generating'
        ]);

        // Submit the image generation task to the Python worker queue API
        try {
            Http::timeout(3)->post(config('admin.vm.base_url') . '/queue-task', [
                'client_id' => $client->id,
                'task_type' => 'generate_image',
                'payload' => [
                    'prompt' => $request->image_prompt,
                    'post_id' => $post->id
                ]
            ]);
        } catch (\Exception $e) {
            \Log::warning("Failed to queue campaign image generation task for post ID {$post->id}: " . $e->getMessage());
        }

        // Log in AuditLog
        AuditLog::create([
            'user_id' => $client->id,
            'action' => "Client launched {$request->platform} affiliate marketing campaign '{$request->title}'",
            'target_table' => 'posts',
            'ip_address' => $request->ip()
        ]);

        // Dispatch notification
        Notification::create([
            'user_id' => $client->id,
            'title' => 'Affiliate Campaign Live! 🚀',
            'message' => "Campaign '{$request->title}' has been successfully broadcast to {$request->platform} outbox.",
            'is_read' => false
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Campaign successfully launched!'
        ]);
    }

    // --- Analytics Mock Engine ---
    private function getClientMockStats($client)
    {
        if ($client->email === 'sarah@jewelrybloom.com') {
            return [
                'reach' => 24150,
                'clicks' => 524,
                'conversions' => 18,
                'commission' => 9000,
                'spend' => 2750,
                'plan' => 'Pro Tier'
            ];
        } elseif ($client->email === 'david@artisanalbeans.coffee') {
            return [
                'reach' => 8420,
                'clicks' => 198,
                'conversions' => 4,
                'commission' => 2000,
                'spend' => 1200,
                'plan' => 'Starter Tier'
            ];
        } elseif ($client->email === 'emma@zenithgrowth.com') {
            return [
                'reach' => 31890,
                'clicks' => 789,
                'conversions' => 31,
                'commission' => 15500,
                'spend' => 4100,
                'plan' => 'Pro Tier'
            ];
        }

        // Default stats for new registrations
        return [
            'reach' => 0,
            'clicks' => 0,
            'conversions' => 0,
            'commission' => 0,
            'spend' => 0,
            'plan' => 'Free Trial'
        ];
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
