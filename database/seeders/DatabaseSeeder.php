<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Project;
use App\Models\Keyword;
use App\Models\PlatformAccount;
use App\Models\Post;
use App\Models\Lead;
use App\Models\Conversation;
use App\Models\AgentMemory;
use App\Models\Meeting;
use App\Models\Notification;
use App\Models\Subscription;
use App\Models\Invoice;
use App\Models\AuditLog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Admin User
        $user = User::create([
            'name' => 'Lakshya Admin',
            'email' => 'admin@lakshya.ai',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        // Mock Client Users
        $client1 = User::create([
            'name' => 'Sarah Miller',
            'email' => 'sarah@jewelrybloom.com',
            'password' => Hash::make('client123'),
            'role' => 'client',
        ]);

        $client2 = User::create([
            'name' => 'David Chen',
            'email' => 'david@artisanalbeans.coffee',
            'password' => Hash::make('client123'),
            'role' => 'client',
        ]);

        $client3 = User::create([
            'name' => 'Emma Watson',
            'email' => 'emma@zenithgrowth.com',
            'password' => Hash::make('client123'),
            'role' => 'client',
        ]);

        // 2. Subscription & Invoices (Admin)
        $subscription = Subscription::create([
            'user_id' => $user->id,
            'tier' => 'Pro',
            'status' => 'Active',
            'limits_json' => [
                'leads_monthly' => 1000,
                'scrapes_daily' => 5000,
                'keywords_limit' => 25,
            ],
            'billing_cycle_ends_at' => Carbon::now()->addDays(23),
        ]);

        // Client Subscriptions
        Subscription::create([
            'user_id' => $client1->id,
            'tier' => 'Pro',
            'status' => 'Active',
            'limits_json' => ['leads_monthly' => 1000, 'scrapes_daily' => 5000, 'keywords_limit' => 25],
            'billing_cycle_ends_at' => Carbon::now()->addDays(20),
        ]);

        Subscription::create([
            'user_id' => $client2->id,
            'tier' => 'Starter',
            'status' => 'Active',
            'limits_json' => ['leads_monthly' => 100, 'scrapes_daily' => 500, 'keywords_limit' => 5],
            'billing_cycle_ends_at' => Carbon::now()->addDays(15),
        ]);

        Subscription::create([
            'user_id' => $client3->id,
            'tier' => 'Pro',
            'status' => 'Active',
            'limits_json' => ['leads_monthly' => 1000, 'scrapes_daily' => 5000, 'keywords_limit' => 25],
            'billing_cycle_ends_at' => Carbon::now()->addDays(28),
        ]);

        Invoice::create([
            'subscription_id' => $subscription->id,
            'amount' => 49.00,
            'status' => 'Paid',
            'invoice_date' => Carbon::now()->subDays(7),
            'pdf_path' => '/invoices/INV-2026-001.pdf',
        ]);

        Invoice::create([
            'subscription_id' => $subscription->id,
            'amount' => 49.00,
            'status' => 'Paid',
            'invoice_date' => Carbon::now()->subMonth()->subDays(7),
            'pdf_path' => '/invoices/INV-2026-002.pdf',
        ]);

        // 3. Projects
        $project1 = Project::create([
            'user_id' => $user->id,
            'name' => 'Acme B2B Outreach',
            'description' => 'Automated B2B lead generation campaign to find startup founders looking for AI integrations and custom software development.',
            'status' => 'Active',
        ]);

        $project2 = Project::create([
            'user_id' => $user->id,
            'name' => 'Zenith Growth Marketing',
            'description' => 'Outreach campaign targeting e-commerce stores looking to scale their paid ads and search engine optimizations.',
            'status' => 'Paused',
        ]);

        // Client Projects
        Project::create([
            'user_id' => $client1->id,
            'name' => 'Sterling Botanical Jewelry',
            'description' => 'Organic outreach and creative campaigns driving traffic to sterling silver botanical jewelry and custom rings.',
            'status' => 'Active',
        ]);

        Project::create([
            'user_id' => $client2->id,
            'name' => 'Artisanal Coffee Roasters',
            'description' => 'Social marketing campaign for single-origin artisanal coffee micro-lots and subscriber conversions.',
            'status' => 'Active',
        ]);

        // 4. Platform Accounts (Scraper Profiles)
        PlatformAccount::create([
            'project_id' => $project1->id,
            'platform' => 'reddit',
            'username' => 'lakshya_scout',
            'session_cookies' => json_encode(['session_id' => 'abc123xyz', 'auth_token' => 'tkn_998877']),
            'status' => 'Active',
            'last_used_at' => Carbon::now()->subMinutes(15),
        ]);

        PlatformAccount::create([
            'project_id' => $project1->id,
            'platform' => 'twitter',
            'username' => 'LakshyaAI',
            'session_cookies' => json_encode(['ct0' => 'foo_ct0_value', 'auth_token' => 'foo_auth_token']),
            'status' => 'Rate Limited',
            'last_used_at' => Carbon::now()->subHours(2),
        ]);

        PlatformAccount::create([
            'project_id' => $project2->id,
            'platform' => 'linkedin',
            'username' => 'lakshya_agency',
            'session_cookies' => null,
            'status' => 'Verification Required',
            'last_used_at' => Carbon::now()->subDays(3),
        ]);

        // 5. Keywords
        $keywords = [
            'need custom software development',
            'looking for developer',
            'hire software development agency',
            'integrate AI chatbot website',
            'outsource app development',
        ];
        foreach ($keywords as $kw) {
            Keyword::create([
                'project_id' => $project1->id,
                'keyword' => $kw,
                'status' => 'Active',
                'last_scraped_at' => Carbon::now()->subMinutes(rand(10, 120)),
            ]);
        }

        Keyword::create([
            'project_id' => $project2->id,
            'keyword' => 'looking for SEO agency',
            'status' => 'Active',
            'last_scraped_at' => Carbon::now()->subDays(1),
        ]);

        // 6. Agent Memories
        AgentMemory::create([
            'project_id' => $project1->id,
            'memory_key' => 'offering_pitch',
            'memory_value' => 'We provide premium custom software development, AI chatbot integrations, and marketing automation solutions to scale B2B businesses.',
            'type' => 'knowledge',
        ]);

        AgentMemory::create([
            'project_id' => $project1->id,
            'memory_key' => 'call_to_action',
            'memory_value' => 'schedule a free 15-minute consultation at https://lakshya.ai/consult',
            'type' => 'knowledge',
        ]);

        // 7. Posts & Leads & Conversations & Meetings
        // Lead 1: New - Reddit
        $post1 = Post::create([
            'project_id' => $project1->id,
            'platform' => 'reddit',
            'external_id' => 't3_1ddxyz1',
            'title' => 'Looking to hire a development agency for custom SaaS',
            'content' => "Hey guys, I am looking to hire a software development agency to build a custom SaaS platform for logistics. We have detailed mockups. Must have experience with Laravel and APIs. Please DM me portfolios. Thanks!",
            'author' => 'u/startup_founder_1',
            'url' => 'https://reddit.com/r/SaaS/comments/1ddxyz1',
            'status' => 'Pending',
            'scraped_at' => Carbon::now()->subHours(1),
        ]);

        Lead::create([
            'post_id' => $post1->id,
            'project_id' => $project1->id,
            'contact_name' => 'u/startup_founder_1',
            'contact_email' => null,
            'score' => 96,
            'intent_category' => 'High Intent - Requesting Agency',
            'status' => 'New',
            'notes' => 'Founder looking to hire custom SaaS developers, mentions Laravel stack compatibility.',
            'generated_reply' => "Hey startup_founder_1! We have built several custom logistics and SaaS systems using Laravel.

We handle end-to-end design and high-performance backend APIs. If you'd like to check out our portfolio and schedule a brief chat, feel free to schedule a free 15-minute consultation at https://lakshya.ai/consult. 

Looking forward to hearing about your project!",
        ]);

        // Lead 2: Discovered (Analyzed but no outreach yet) - Twitter
        $post2 = Post::create([
            'project_id' => $project1->id,
            'platform' => 'twitter',
            'external_id' => '17992288337744882',
            'title' => null,
            'content' => "We need to integrate an AI chatbot on our marketing website to automate customer support routing. Any recommendations for agencies who can build this custom?",
            'author' => '@marketing_dan',
            'url' => 'https://twitter.com/marketing_dan/status/17992288337744882',
            'status' => 'Qualified',
            'scraped_at' => Carbon::now()->subHours(3),
        ]);

        Lead::create([
            'post_id' => $post2->id,
            'project_id' => $project1->id,
            'contact_name' => '@marketing_dan',
            'contact_email' => 'dan@converted.agency',
            'score' => 92,
            'intent_category' => 'Problem Identified - AI Integration',
            'status' => 'Discovered',
            'notes' => 'Wants custom AI chatbot integration for marketing site support routing.',
            'generated_reply' => "Hi Dan! We specialize in custom AI integrations and customer support chatbots that link directly to database actions. 

I would love to walk you through some similar workflows we've built. Let's connect: schedule a free 15-minute consultation at https://lakshya.ai/consult.",
        ]);

        // Lead 3: Contacted - Reddit
        $post3 = Post::create([
            'project_id' => $project1->id,
            'platform' => 'reddit',
            'external_id' => 't3_1ddabc2',
            'title' => 'Need help setting up automated marketing flows',
            'content' => 'I run an agency and we need help outsourcing our app development and setting up automated outreach flows. Who are some reliable partners?',
            'author' => 'u/code_builder',
            'url' => 'https://reddit.com/r/design/comments/1ddabc2',
            'status' => 'Qualified',
            'scraped_at' => Carbon::now()->subHours(6),
        ]);

        $lead3 = Lead::create([
            'post_id' => $post3->id,
            'project_id' => $project1->id,
            'contact_name' => 'u/code_builder',
            'contact_email' => null,
            'score' => 88,
            'intent_category' => 'High Intent - Outsourcing Dev',
            'status' => 'Contacted',
            'notes' => 'Agency owner looking to outsource mobile app development and configure automations.',
            'generated_reply' => "Hey code_builder! We partner with agencies to handle custom development and automate back-office workflows. 

You can review our software services and book a quick sync here: schedule a free 15-minute consultation at https://lakshya.ai/consult. Let's discuss a partnership!",
        ]);

        Conversation::create([
            'lead_id' => $lead3->id,
            'project_id' => $project1->id,
            'messages' => [
                ['sender' => 'system', 'message' => 'Automated initial outreach sent on Reddit.', 'timestamp' => Carbon::now()->subHours(5)->toDateTimeString()],
                ['sender' => 'agent', 'message' => "Hey code_builder! We partner with agencies... book a quick sync here: https://lakshya.ai/consult", 'timestamp' => Carbon::now()->subHours(5)->toDateTimeString()]
            ],
            'last_message_at' => Carbon::now()->subHours(5),
        ]);

        // Lead 4: Qualified (Interested & replied) - Reddit
        $post4 = Post::create([
            'project_id' => $project1->id,
            'platform' => 'reddit',
            'external_id' => 't3_1ddkk88',
            'title' => 'Looking for agency to outsource iOS and Android app development',
            'content' => 'We are a funded startup looking to outsource the build of our initial iOS and Android apps. Budget is around $35k. Need clean code and clean design.',
            'author' => 'u/saas_bootstrapper',
            'url' => 'https://reddit.com/r/SaaS/comments/1ddkk88',
            'status' => 'Qualified',
            'scraped_at' => Carbon::now()->subHours(12),
        ]);

        $lead4 = Lead::create([
            'post_id' => $post4->id,
            'project_id' => $project1->id,
            'contact_name' => 'u/saas_bootstrapper',
            'contact_email' => 'josh@saasbootcamp.com',
            'score' => 97,
            'intent_category' => 'Product Fit - Outsource Mobile Build',
            'status' => 'Qualified',
            'notes' => 'Outsourcing iOS/Android app development with clear budget range. Excellent fit.',
            'generated_reply' => "Hey saas_bootstrapper! We specialize in premium mobile app builds using clean native and hybrid frameworks.

Let's do a quick call to check out your mockups: schedule a free 15-minute consultation at https://lakshya.ai/consult.",
        ]);

        Conversation::create([
            'lead_id' => $lead4->id,
            'project_id' => $project1->id,
            'messages' => [
                ['sender' => 'system', 'message' => 'Automated initial outreach sent.', 'timestamp' => Carbon::now()->subHours(11)->toDateTimeString()],
                ['sender' => 'agent', 'message' => "Hey saas_bootstrapper! We build clean mobile apps... schedule a free 15-minute consultation at https://lakshya.ai/consult.", 'timestamp' => Carbon::now()->subHours(11)->toDateTimeString()],
                ['sender' => 'lead', 'message' => "Hey! Checked your portfolio and it looks great. Yes, let's schedule a call to review our wireframes.", 'timestamp' => Carbon::now()->subHours(8)->toDateTimeString()],
                ['sender' => 'agent', 'message' => "Awesome, I'll send over a calendar link to book a slot.", 'timestamp' => Carbon::now()->subHours(7)->toDateTimeString()],
                ['sender' => 'lead', 'message' => "Sounds good, looking forward to it.", 'timestamp' => Carbon::now()->subHours(6)->toDateTimeString()]
            ],
            'last_message_at' => Carbon::now()->subHours(6),
        ]);

        // Lead 5: Closed (Meeting Booked) - Twitter
        $post5 = Post::create([
            'project_id' => $project1->id,
            'platform' => 'twitter',
            'external_id' => '1799330044882772',
            'title' => null,
            'content' => "Need an expert to integrate an AI lead qualifier into our custom CRM system. Time-sensitive contract.",
            'author' => '@julia_growth',
            'url' => 'https://twitter.com/julia_growth/status/1799330044882772',
            'status' => 'Qualified',
            'scraped_at' => Carbon::now()->subDays(1),
        ]);

        $lead5 = Lead::create([
            'post_id' => $post5->id,
            'project_id' => $project1->id,
            'contact_name' => '@julia_growth',
            'contact_email' => 'julia@growthhack.co',
            'score' => 95,
            'intent_category' => 'Time Sensitive - AI Integration Service',
            'status' => 'Closed',
            'notes' => 'Meeting scheduled to pitch custom B2B AI integrations.',
            'generated_reply' => "Hi Julia! We have integrated AI qualifiers into several custom CRM dashboards. We can build this quickly. Book a slot: https://lakshya.ai/consult",
        ]);

        Conversation::create([
            'lead_id' => $lead5->id,
            'project_id' => $project1->id,
            'messages' => [
                ['sender' => 'agent', 'message' => "Hi Julia! We have integrated AI qualifiers... book a slot here: https://lakshya.ai/consult", 'timestamp' => Carbon::now()->subDays(1)->toDateTimeString()],
                ['sender' => 'lead', 'message' => "Awesome. Let's do a call tomorrow afternoon.", 'timestamp' => Carbon::now()->subHours(20)->toDateTimeString()],
                ['sender' => 'agent', 'message' => "Great! Booked a calendar slot for tomorrow at 2 PM.", 'timestamp' => Carbon::now()->subHours(18)->toDateTimeString()]
            ],
            'last_message_at' => Carbon::now()->subHours(18),
        ]);

        Meeting::create([
            'lead_id' => $lead5->id,
            'project_id' => $project1->id,
            'scheduled_at' => Carbon::tomorrow()->setHour(14)->setMinute(0)->setSecond(0),
            'duration_minutes' => 15,
            'meeting_link' => 'https://meet.google.com/lakshya-session',
            'status' => 'Scheduled',
        ]);

        // 8. Notifications
        Notification::create([
            'user_id' => $user->id,
            'title' => 'New Lead Qualified',
            'message' => 'AI classified u/saas_bootstrapper as High Intent (97/100). Auto-outreach drafted.',
            'is_read' => false,
        ]);

        Notification::create([
            'user_id' => $user->id,
            'title' => 'Rate Limit Warning',
            'message' => 'Platform account @LakshyaAI has hit Twitter API limits. Crawler paused for 4 hours.',
            'is_read' => false,
        ]);

        Notification::create([
            'user_id' => $user->id,
            'title' => 'Meeting Booked',
            'message' => 'Meeting scheduled with Julia Growth for tomorrow at 2:00 PM.',
            'is_read' => true,
        ]);

        // 9. Audit Logs
        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'User logged in',
            'target_table' => 'users',
            'ip_address' => '127.0.0.1',
        ]);

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'Added keyword "integrate AI chatbot website"',
            'target_table' => 'keywords',
            'ip_address' => '127.0.0.1',
        ]);

        AuditLog::create([
            'user_id' => null, // background system job
            'action' => 'Background Agent Scrape run - Discovered 12 posts, qualified 2 leads',
            'target_table' => 'posts',
            'ip_address' => '192.168.10.4',
        ]);
    }
}

