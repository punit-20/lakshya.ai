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
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ComprehensiveEdgeCasesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Disable CSRF validation
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);

        // Fake Google Gemini AI requests
        \Illuminate\Support\Facades\Http::fake([
            'generativelanguage.googleapis.com/*' => function (\Illuminate\Http\Client\Request $request) {
                $data = [
                    'linkedin' => 'Mock LinkedIn Value',
                    'twitter' => 'Mock Twitter Value',
                    'telegram' => 'Mock Telegram Value',
                    'facebook' => 'Mock Facebook Value',
                    'reddit' => 'Mock Reddit Value',
                    'image_prompt' => 'Mock Graphic Image Prompt'
                ];
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

        // Seed the standard database records
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
    }

    /** @test */
    public function client_registration_validations_and_boundary_checks()
    {
        // 1. Invalid passwords mismatch confirmation
        $response = $this->post('/register', [
            'name' => 'Failed Register',
            'email' => 'fail@jewelry.com',
            'password' => 'password123',
            'password_confirmation' => 'mismatch_pass',
        ]);
        $response->assertSessionHasErrors(['password']);
        $this->assertGuest();

        // 2. Client role cannot access admin dashboard portal
        $client = User::create([
            'name' => 'Sarah Client',
            'email' => 'sarah_client@jewelry.com',
            'password' => Hash::make('pass123'),
            'role' => 'client',
        ]);
        Subscription::create([
            'user_id' => $client->id,
            'tier' => 'Starter',
            'status' => 'Active',
            'credits' => 10,
        ]);

        $response = $this->actingAs($client)->get('/admin/dashboard');
        // A client user gets blocked with 403 Forbidden on admin prefix
        $response->assertStatus(403);
    }

    /** @test */
    public function admin_project_switching_restrictions()
    {
        $admin = User::where('role', 'admin')->first();
        $client = User::where('role', 'client')->first(); // David Chen / Sarah Miller

        // Fetch client project
        $clientProject = Project::where('user_id', $client->id)->first();
        $this->assertNotNull($clientProject);

        // 1. Admin attempts to switch to an unauthorized client project
        $response = $this->actingAs($admin)->postJson('/admin/switch-project', [
            'project_id' => $clientProject->id
        ]);
        $response->assertStatus(403)->assertJson([
            'success' => false,
            'error' => 'Unauthorized project access.'
        ]);

        // 2. Admin attempts to switch to a non-existent project id
        $response = $this->actingAs($admin)->postJson('/admin/switch-project', [
            'project_id' => 99999
        ]);
        $response->assertStatus(500)->assertJson([
            'success' => false,
            'error' => 'Failed to switch project.'
        ]);
    }

    /** @test */
    public function crm_lead_actions_and_missing_edge_cases()
    {
        $admin = User::where('role', 'admin')->first();
        
        // 1. Fetch details of a non-existent Lead ID
        $response = $this->actingAs($admin)->getJson('/admin/crm/details/887766');
        $response->assertStatus(404)->assertJson([
            'success' => false,
            'error' => 'Lead not found.'
        ]);

        // 2. Update status of lead with invalid status name
        $lead = Lead::first();
        $response = $this->actingAs($admin)->postJson('/admin/crm/status', [
            'lead_id' => $lead->id,
            'status' => 'SuperClosed' // Invalid status value
        ]);
        $response->assertStatus(500)->assertJson([
            'success' => false,
            'error' => 'Failed to update lead status.'
        ]);

        // 3. Schedule meeting with missing values (should return validation error/500)
        $response = $this->actingAs($admin)->postJson('/admin/crm/meeting', [
            'lead_id' => $lead->id
        ]);
        $response->assertStatus(500);

        // 4. Generate AI reply draft for non-existent lead ID (validation error/500)
        $response = $this->actingAs($admin)->postJson('/admin/crm/generate', [
            'lead_id' => 99992
        ]);
        $response->assertStatus(500);
    }

    /** @test */
    public function platform_settings_validation_and_saving()
    {
        $admin = User::where('role', 'admin')->first();

        // 1. Access Settings
        $response = $this->actingAs($admin)->get('/admin/settings');
        $response->assertStatus(200);

        // 2. Save platform scraper settings with missing username and platform
        $response = $this->actingAs($admin)->post('/admin/settings/account', [
            'platform' => '',
            'username' => ''
        ]);
        $response->assertSessionHasErrors(['platform', 'username']);

        // 3. Save settings successfully
        $response = $this->actingAs($admin)->post('/admin/settings/account', [
            'platform' => 'reddit',
            'username' => 'scout_test',
            'session_cookies' => json_encode(['session_id' => 'foo'])
        ]);
        $response->assertRedirect('/admin/settings');
        
        $acct = \App\Models\PlatformAccount::where('username', 'scout_test')->first();
        $this->assertNotNull($acct);
        $this->assertEquals('reddit', $acct->platform);
    }

    /** @test */
    public function nsfw_blocked_requests_deduction_bounds()
    {
        // 1. Create client user with exactly 4 credits (less than 5 credits NSFW penalty)
        $client = User::create([
            'name' => 'Poor Client',
            'email' => 'poor@outreach.com',
            'password' => Hash::make('pass123'),
            'role' => 'client',
        ]);
        $sub = Subscription::create([
            'user_id' => $client->id,
            'tier' => 'Starter',
            'status' => 'Active',
            'credits' => 4,
        ]);

        // 2. Perform NSFW request containing blocked terms
        $response = $this->actingAs($client)->postJson('/client/marketing/generate-social', [
            'business_description' => 'We sell sexually explicit adult pornography toys and vaginas',
            'tone' => 'Sensual',
            'target_audience' => 'Adult buyers',
            'cta' => 'Buy now'
        ]);

        // Asserts status 422 blocked
        $response->assertStatus(422);
        
        // Asserts credits balance is capped at 0 (never negative)
        $this->assertEquals(0, $sub->refresh()->credits);
    }
}
