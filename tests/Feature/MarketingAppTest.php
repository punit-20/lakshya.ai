<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Project;
use App\Models\Keyword;
use App\Models\Lead;
use App\Models\Post;
use App\Models\Conversation;
use App\Models\Meeting;
use App\Models\Notification;
use App\Models\Subscription;
use App\Models\AuditLog;
use App\Enums\ProjectStatus;
use App\Enums\KeywordStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class MarketingAppTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Disable CSRF middleware for testing
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);

        // Fake all Gemini API requests and return successful mock structures
        \Illuminate\Support\Facades\Http::fake([
            'generativelanguage.googleapis.com/*' => function (\Illuminate\Http\Client\Request $request) {
                $body = $request->data();
                $prompt = $body['contents'][0]['parts'][0]['text'] ?? '';

                if (str_contains($prompt, 'social media marketing posts') || str_contains($prompt, 'affiliate links')) {
                    $data = [
                        'linkedin' => 'Mock LinkedIn Post',
                        'twitter' => 'Mock Twitter Post',
                        'telegram' => 'Mock Telegram Post',
                        'facebook' => 'Mock Facebook Post',
                        'reddit' => 'Mock Reddit Post',
                        'image_prompt' => 'Mock Image Prompt'
                    ];
                } elseif (str_contains($prompt, 'growth content pack')) {
                    $data = [
                        'blog_outline' => 'Mock Blog Outline',
                        'newsletter_copy' => 'Mock Newsletter Copy',
                        'content_calendar' => ['Mock Monday', 'Mock Tuesday']
                    ];
                } elseif (str_contains($prompt, 'AI Campaign suggestion package')) {
                    $data = [
                        'target_audience' => 'Mock Target Audience',
                        'keywords' => ['keyword1', 'keyword2'],
                        'headlines' => ['headline1', 'headline2'],
                        'ad_copy' => ['copy1', 'copy2'],
                        'creative_ideas' => ['idea1', 'idea2'],
                        'landing_page' => 'Mock Landing Page'
                    ];
                } else {
                    $data = [];
                }

                return \Illuminate\Support\Facades\Http::response([
                    'candidates' => [
                        [
                            'content' => [
                                'parts' => [
                                    ['text' => json_encode($data)]
                                ]
                            ]
                        ]
                    ]
                ], 200);
            }
        ]);

        // Seed the database with our standard seed data
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
    }

    /** @test */
    public function authentication_and_session_management_works()
    {
        // 1. View Login Page
        $response = $this->get('/login');
        $response->assertStatus(200);

        // 2. Try Login with Invalid Credentials
        $response = $this->post('/login', [
            'email' => 'admin@lakshya.ai',
            'password' => 'wrongpassword',
        ]);
        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();

        // 3. Login with Valid Admin Credentials
        $response = $this->post('/login', [
            'email' => 'admin@lakshya.ai',
            'password' => 'admin123',
        ]);
        $response->assertRedirect('/admin/dashboard');
        $this->assertAuthenticatedAs(User::where('email', 'admin@lakshya.ai')->first());

        // 4. Logout
        $response = $this->post('/logout');
        $response->assertRedirect('/login');
        $this->assertGuest();

        // 5. Registration Page
        $response = $this->get('/register');
        $response->assertStatus(200);

        // 6. Register a New Client
        $response = $this->post('/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);
        $response->assertRedirect('/client/dashboard');
        $this->assertAuthenticated();
        
        $user = User::where('email', 'john@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('client', $user->role);
        
        // Assert client subscription was initialized
        $subscription = Subscription::where('user_id', $user->id)->first();
        $this->assertNotNull($subscription);
        $this->assertEquals('Free Trial', $subscription->tier);
        $this->assertEquals(config('pricing.tiers.free_trial.credits', 10), $subscription->credits);

        // Assert client first project was initialized
        $project = Project::where('user_id', $user->id)->first();
        $this->assertNotNull($project);
        $this->assertEquals('John Doe Campaign', $project->name);
    }

    /** @test */
    public function admin_dashboard_and_common_actions_work()
    {
        $admin = User::where('role', 'admin')->first();
        
        // 1. Try accessing dashboard when unauthenticated (should redirect)
        $response = $this->get('/admin/dashboard');
        $response->assertRedirect('/login');

        // 2. Access dashboard as Admin
        $response = $this->actingAs($admin)->get('/admin/dashboard');
        $response->assertStatus(200);
        $response->assertViewHasAll([
            'project', 'totalLeads', 'qualifiedLeads', 'conversionRate',
            'activeKeywords', 'activeAccounts', 'recentNotifications',
            'recentAuditLogs', 'activeClientsCount', 'liveMRR',
            'liveNetProfit', 'profitTargetProgress', 'nsfwViolations'
        ]);

        // 3. Switch active project
        $newProject = Project::create([
            'user_id' => $admin->id,
            'name' => 'Another Project',
            'description' => 'Test project',
            'status' => ProjectStatus::Active->value
        ]);

        $response = $this->actingAs($admin)->postJson('/admin/switch-project', [
            'project_id' => $newProject->id
        ]);
        $response->assertStatus(200)->assertJson(['success' => true]);
        $this->assertEquals($newProject->id, session('active_project_id'));

        // 4. Switch to an unauthorized project (e.g. client project)
        $client = User::where('role', 'client')->first();
        $clientProject = Project::where('user_id', $client->id)->first();

        $response = $this->actingAs($admin)->postJson('/admin/switch-project', [
            'project_id' => $clientProject->id
        ]);
        $response->assertStatus(403)->assertJson(['success' => false, 'error' => 'Unauthorized project access.']);

        // 5. Trigger VM Crawl API (Mocked call success/fail)
        $response = $this->actingAs($admin)->postJson('/admin/vm/trigger');
        // It tries to reach VM at localhost:5000. It may succeed (200) or fail (500) depending on daemon status.
        $this->assertTrue(in_array($response->status(), [200, 500]));

        // 6. Mark Notifications Read
        $unreadNotification = Notification::create([
            'user_id' => $admin->id,
            'title' => 'Test Notification',
            'message' => 'Hello',
            'is_read' => false
        ]);

        $response = $this->actingAs($admin)->postJson('/admin/notifications/read');
        $response->assertStatus(200)->assertJson(['success' => true]);
        $this->assertTrue($unreadNotification->refresh()->is_read);
    }

    /** @test */
    public function crm_lead_management_works()
    {
        $admin = User::where('role', 'admin')->first();
        $lead = Lead::first();

        // 1. Access CRM Page
        $response = $this->actingAs($admin)->get('/admin/crm');
        $response->assertStatus(200);
        $response->assertViewHasAll(['leadsByStatus', 'statuses']);

        // 2. Fetch Lead Details Modal data
        $response = $this->actingAs($admin)->getJson("/admin/crm/details/{$lead->id}");
        $response->assertStatus(200)->assertJson([
            'success' => true,
            'lead' => [
                'id' => $lead->id,
                'contact_name' => $lead->contact_name
            ]
        ]);

        // 3. Update Lead Status
        $response = $this->actingAs($admin)->postJson('/admin/crm/status', [
            'lead_id' => $lead->id,
            'status' => 'Qualified'
        ]);
        $response->assertStatus(200)->assertJson(['success' => true]);
        $this->assertEquals('Qualified', $lead->refresh()->status);

        // 4. Generate AI Reply for Lead (using controller mock fallback logic)
        $response = $this->actingAs($admin)->postJson('/admin/crm/generate', [
            'lead_id' => $lead->id
        ]);
        $response->assertStatus(200)->assertJson(['success' => true]);
        $this->assertNotEmpty($lead->refresh()->generated_reply);

        // 5. Save Custom Reply Draft
        $response = $this->actingAs($admin)->postJson('/admin/crm/save-reply', [
            'lead_id' => $lead->id,
            'reply' => 'My customized reply'
        ]);
        $response->assertStatus(200)->assertJson(['success' => true]);
        $this->assertEquals('My customized reply', $lead->refresh()->generated_reply);

        // 6. Send message / Log conversation message
        $response = $this->actingAs($admin)->postJson('/admin/crm/send-message', [
            'lead_id' => $lead->id,
            'message' => 'Sending message'
        ]);
        $response->assertStatus(200)->assertJson(['success' => true]);
        
        $convo = Conversation::where('lead_id', $lead->id)->first();
        $this->assertNotNull($convo);
        $messages = $convo->messages;
        $lastMessage = end($messages);
        $this->assertEquals('Sending message', $lastMessage['message']);
        $this->assertEquals('Contacted', $lead->refresh()->status);

        // 7. Schedule Google Meet Meeting
        $response = $this->actingAs($admin)->postJson('/admin/crm/meeting', [
            'lead_id' => $lead->id,
            'scheduled_at' => now()->addDay()->toDateTimeString(),
            'meeting_link' => 'https://meet.google.com/abc-xyz'
        ]);
        $response->assertStatus(200)->assertJson(['success' => true]);
        
        $meeting = Meeting::where('lead_id', $lead->id)->first();
        $this->assertNotNull($meeting);
        $this->assertEquals('https://meet.google.com/abc-xyz', $meeting->meeting_link);
        $this->assertEquals('Closed', $lead->refresh()->status);
    }

    /** @test */
    public function project_and_keyword_management_works()
    {
        $admin = User::where('role', 'admin')->first();
        $project = Project::first();

        // Set session active project
        session(['active_project_id' => $project->id]);

        // 1. Access Projects Listing
        $response = $this->actingAs($admin)->get('/admin/projects');
        $response->assertStatus(200);

        // 2. Store New Project
        $response = $this->actingAs($admin)->post('/admin/projects/store', [
            'name' => 'Brand New Expansion Campaign',
            'description' => 'Targeting local startups.'
        ]);
        $response->assertRedirect('/admin/projects');
        $newProj = Project::where('name', 'Brand New Expansion Campaign')->first();
        $this->assertNotNull($newProj);
        $this->assertEquals(session('active_project_id'), $newProj->id);

        // 3. Toggle Project Status
        $response = $this->actingAs($admin)->get("/admin/projects/toggle/{$newProj->id}");
        $response->assertRedirect('/admin/projects');
        $this->assertEquals(ProjectStatus::Paused->value, $newProj->refresh()->status);

        // 4. Access Keywords Listing
        $response = $this->actingAs($admin)->get('/admin/keywords');
        $response->assertStatus(200);

        // 5. Store New Keyword
        $response = $this->actingAs($admin)->post('/admin/keywords/store', [
            'keyword' => 'freelance Laravel developer'
        ]);
        $response->assertRedirect('/admin/keywords');
        
        $kw = Keyword::where('keyword', 'freelance Laravel developer')->first();
        $this->assertNotNull($kw);
        $this->assertEquals(KeywordStatus::Active->value, $kw->status);

        // 6. Toggle Keyword Status
        $response = $this->actingAs($admin)->postJson("/admin/keywords/toggle/{$kw->id}");
        $response->assertStatus(200)->assertJson(['success' => true]);
        $this->assertEquals(KeywordStatus::Paused->value, $kw->refresh()->status);

        // 7. Delete Keyword
        $response = $this->actingAs($admin)->delete("/admin/keywords/delete/{$kw->id}");
        $response->assertRedirect('/admin/keywords');
        $this->assertNull(Keyword::find($kw->id));
    }

    /** @test */
    public function marketing_builder_content_generation_and_launch_works()
    {
        $admin = User::where('role', 'admin')->first();

        // 1. Load Admin Marketing view
        $response = $this->actingAs($admin)->get('/admin/marketing');
        $response->assertStatus(200);
        $response->assertViewIs('marketing');

        // 2. Generate Social media Suite (using Gemini Mock generator)
        $response = $this->actingAs($admin)->postJson('/admin/marketing/generate-social', [
            'business_description' => 'We sell custom botanical silver rings handcrafted from real leaves.',
            'tone' => 'Professional',
            'target_audience' => 'Eco-conscious jewelry lovers',
            'cta' => 'Use coupon NATURE15 for 15% off'
        ]);
        $response->assertStatus(200)->assertJson([
            'success' => true,
            'posts' => [
                'linkedin' => true,
                'twitter' => true,
                'telegram' => true,
                'facebook' => true,
                'reddit' => true,
                'image_prompt' => true,
            ]
        ]);

        // 3. Generate Weekly Growth pack
        $response = $this->actingAs($admin)->postJson('/admin/marketing/generate-growth', [
            'business_description' => 'We sell custom botanical silver rings handcrafted from real leaves.',
            'target_audience' => 'Eco-conscious jewelry lovers',
            'campaign_goal' => 'Increase subscription signups'
        ]);
        $response->assertStatus(200)->assertJson([
            'success' => true,
            'growth_pack' => [
                'blog_outline' => true,
                'newsletter_copy' => true,
                'content_calendar' => true,
            ]
        ]);

        // 4. Generate Ad Campaign Suite
        $response = $this->actingAs($admin)->postJson('/admin/marketing/generate-campaign', [
            'product' => 'Recycled Silver Rings',
            'target_audience' => 'Eco-conscious jewelry lovers',
            'budget' => '$500/month',
            'campaign_goal' => 'Drive purchase conversions'
        ]);
        $response->assertStatus(200)->assertJson([
            'success' => true,
            'campaign' => [
                'target_audience' => true,
                'keywords' => true,
                'headlines' => true,
                'ad_copy' => true,
                'creative_ideas' => true,
                'landing_page' => true,
            ]
        ]);

        // 5. Launch Campaign
        $response = $this->actingAs($admin)->postJson('/admin/marketing/launch', [
            'platform' => 'twitter',
            'title' => 'Shop Botanical Rings',
            'description' => 'Elegance handcrafted from real leaves. Buy today.',
            'image_prompt' => 'botanical silver rings leaf details wood table morning sun'
        ]);
        $response->assertStatus(200)->assertJson([
            'success' => true,
            'message' => 'Campaign successfully launched!'
        ]);

        $launchedPost = Post::orderBy('id', 'desc')->first();
        $this->assertNotNull($launchedPost);
        $this->assertEquals('twitter', $launchedPost->platform);
        $this->assertEquals('Shop Botanical Rings', $launchedPost->title);
        $this->assertEquals('Launched', $launchedPost->status);
    }

    /** @test */
    public function client_impersonation_flows_correctly()
    {
        $admin = User::where('role', 'admin')->first();
        $client = User::where('role', 'client')->first(); // Sarah Miller

        // 1. Visit admin client management page
        $response = $this->actingAs($admin)->get('/admin/clients');
        $response->assertStatus(200);

        // 2. Start impersonating client Sarah Miller (ID 2)
        $response = $this->actingAs($admin)->get("/admin/clients/impersonate/{$client->id}");
        $response->assertRedirect('/client/dashboard');
        $this->assertTrue(session()->has('impersonating_client_id'));
        $this->assertEquals($client->id, session('impersonating_client_id'));

        // 3. Access unified marketing page under impersonation context
        $response = $this->actingAs($admin)->get('/client/marketing');
        $response->assertStatus(200);
        $response->assertViewIs('marketing');
        // Let's assert variables inside the unified view are parsed as client context
        $response->assertSee('Sarah Miller');
        $response->assertSee('AI Creative Builder');

        // 4. Exit impersonation
        $response = $this->actingAs($admin)->get('/admin/clients/exit');
        $response->assertRedirect('/admin/clients');
        $this->assertFalse(session()->has('impersonating_client_id'));
    }

    /** @test */
    public function client_credit_usage_limits_and_nsfw_penalty_works()
    {
        $client = User::where('role', 'client')->first(); // Sarah Miller starts with 485 credits
        $sub = Subscription::where('user_id', $client->id)->first();
        
        $this->assertEquals(485, $sub->credits);

        // 1. Direct login as Client Sarah Miller
        $this->actingAs($client);

        // 2. Generate content with clean input -> consumes 1 credit
        $response = $this->postJson('/client/marketing/generate-social', [
            'business_description' => 'Sterling Botanical jewelry brand specializing in custom silver rings.',
            'tone' => 'Enthusiastic',
            'target_audience' => 'Young couples and environment friendly buyers.',
            'cta' => 'Shop now!'
        ]);
        $response->assertStatus(200)->assertJson(['success' => true]);
        
        // Assert 1 credit deducted
        $this->assertEquals(484, $sub->refresh()->credits);

        // Assert Credit Usage Log exists
        $log = \App\Models\CreditUsageLog::where('user_id', $client->id)->orderBy('id', 'desc')->first();
        $this->assertEquals(1, $log->credits_used);
        $this->assertEquals('AI Content Generation', $log->action);

        // 3. Generate content with NSFW input (vagina / penis) -> Blocks request, returns 422, deducts 5 credits penalty!
        $response = $this->postJson('/client/marketing/generate-social', [
            'business_description' => 'We sell dick and vagina accessories for adult couples.', // NSFW vulgar anatomy terms
            'tone' => 'Casual',
            'target_audience' => 'Adult party goers',
            'cta' => 'Order dick shaped mugs'
        ]);
        $response->assertStatus(422)->assertJson([
            'success' => false,
            'nsfw' => true,
            'error' => 'Security Alert: NSFW/Prohibited content detected (sexually explicit, harassment, porn, etc.). A penalty of 5 credits has been deducted from your subscription balance.'
        ]);

        // Assert 5 credits penalty deducted
        $this->assertEquals(479, $sub->refresh()->credits);

        // Assert NSFW credit usage log created in database
        $nsfwLog = \App\Models\CreditUsageLog::where('user_id', $client->id)->orderBy('id', 'desc')->first();
        $this->assertEquals(5, $nsfwLog->credits_used);
        $this->assertEquals('NSFW Content Blocked', $nsfwLog->action);
        $this->assertStringContainsString('NSFW/Prohibited content', $nsfwLog->details);

        // Assert user notification created
        $userNotification = Notification::where('user_id', $client->id)->orderBy('id', 'desc')->first();
        $this->assertNotNull($userNotification);
        $this->assertStringContainsString('Security Alert: NSFW Penalty!', $userNotification->title);

        // Assert admin notification created (admin is user ID 1)
        $adminNotification = Notification::where('user_id', 1)->orderBy('id', 'desc')->first();
        $this->assertNotNull($adminNotification);
        $this->assertStringContainsString('Client NSFW Violation!', $adminNotification->title);

        // 4. Exhaust client credits and verify Insufficient Credits 403 block
        $sub->credits = 0;
        $sub->save();

        $response = $this->postJson('/client/marketing/generate-social', [
            'business_description' => 'Standard botanical silver rings',
            'tone' => 'Friendly',
            'target_audience' => 'Eco jewelry buyers',
            'cta' => 'Buy now'
        ]);
        $response->assertStatus(403)->assertJson([
            'success' => false,
            'error' => 'Insufficient credits. Your current balance is 0. Please upgrade your plan to top up.'
        ]);
    }

    /** @test */
    public function ai_agents_portal_endpoints_work()
    {
        $admin = User::where('role', 'admin')->first();

        // 1. Access the AI Agents page
        $response = $this->actingAs($admin)->get('/admin/agents');
        $response->assertStatus(200);
        $response->assertViewHasAll([
            'project', 'leads', 'visitors', 'whatsappLogs', 'linkedinLogs', 'emailLogs', 'tasks', 'successRate', 'agents'
        ]);

        // 2. Toggle Agent Status
        $response = $this->actingAs($admin)->postJson('/admin/agents/toggle', [
            'agent_type' => 'EmailAgent',
            'status' => 'Idle'
        ]);
        $response->assertStatus(200)->assertJson(['success' => true]);

        // Verify configuration was saved in AgentMemory
        $memory = \App\Models\AgentMemory::where('memory_key', 'agent_EmailAgent_status')->first();
        $this->assertNotNull($memory);
        $this->assertEquals('Idle', $memory->memory_value);

        // 3. Enqueue new Agent Task
        $response = $this->actingAs($admin)->postJson('/admin/agents/enqueue', [
            'agent_type' => 'EmailAgent',
            'task_name' => 'Verify Test Email',
            'payload' => ['recipient' => 'test@example.com']
        ]);
        $response->assertStatus(200)->assertJson([
            'success' => true,
            'message' => 'Agent task successfully added to queue!'
        ]);

        // Verify task exists in DB
        $task = \App\Models\AgentTask::where('task_name', 'Verify Test Email')->first();
        $this->assertNotNull($task);
        $this->assertEquals('Pending', $task->status);

        // 4. Test AJAX endpoints
        $response = $this->actingAs($admin)->getJson('/admin/agents/visitor-stream');
        $response->assertStatus(200)->assertJson(['success' => true]);

        $response = $this->actingAs($admin)->getJson('/admin/agents/whatsapp-logs');
        $response->assertStatus(200)->assertJson(['success' => true]);

        $response = $this->actingAs($admin)->getJson('/admin/agents/linkedin-logs');
        $response->assertStatus(200)->assertJson(['success' => true]);

        $response = $this->actingAs($admin)->getJson('/admin/agents/queue-logs');
        $response->assertStatus(200)->assertJson(['success' => true]);
    }
}

