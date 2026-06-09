<?php

namespace App\Http\Controllers;

use App\Http\Traits\ActiveProjectTrait;
use App\Models\Project;
use App\Models\Keyword;
use App\Models\PlatformAccount;
use App\Models\Subscription;
use App\Models\Invoice;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class CampaignController extends Controller
{
    use ActiveProjectTrait;

    // --- Projects Management ---
    public function projects()
    {
        $projects = Project::withCount(['leads', 'keywords'])->get();
        return view('admin.projects', compact('projects'));
    }

    public function storeProject(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        $project = Project::create([
            'user_id' => $this->getAuthUserId(),
            'name' => $request->name,
            'description' => $request->description,
            'status' => 'Active'
        ]);

        AuditLog::create([
            'user_id' => $this->getAuthUserId(),
            'action' => "Created project '{$project->name}'",
            'target_table' => 'projects',
            'ip_address' => $request->ip()
        ]);

        session(['active_project_id' => $project->id]);

        return redirect()->route('admin.projects')->with('info', 'Project created successfully and set as active!');
    }

    public function toggleProject($id)
    {
        $project = Project::findOrFail($id);
        $project->status = $project->status == 'Active' ? 'Paused' : 'Active';
        $project->save();

        AuditLog::create([
            'user_id' => $this->getAuthUserId(),
            'action' => "Toggled status of project '{$project->name}' to {$project->status}",
            'target_table' => 'projects',
            'ip_address' => request()->ip()
        ]);

        return redirect()->route('admin.projects')->with('info', "Project '{$project->name}' status updated to {$project->status}.");
    }

    // --- Keywords Management ---
    public function keywords()
    {
        $projectId = $this->getActiveProjectId();
        $project = Project::findOrFail($projectId);
        $keywords = Keyword::where('project_id', $projectId)->get();

        return view('admin.keywords', compact('project', 'keywords'));
    }

    public function storeKeyword(Request $request)
    {
        $request->validate([
            'keyword' => 'required|string|max:255'
        ]);

        $projectId = $this->getActiveProjectId();
        
        $kw = Keyword::create([
            'project_id' => $projectId,
            'keyword' => $request->keyword,
            'status' => 'Active'
        ]);

        AuditLog::create([
            'user_id' => $this->getAuthUserId(),
            'action' => "Added keyword '{$kw->keyword}' to project ID {$projectId}",
            'target_table' => 'keywords',
            'ip_address' => $request->ip()
        ]);

        return redirect()->route('admin.keywords')->with('info', "Keyword '{$kw->keyword}' added!");
    }

    public function toggleKeyword($id)
    {
        try {
            $kw = Keyword::findOrFail($id);
            $kw->status = $kw->status == 'Active' ? 'Paused' : 'Active';
            $kw->save();

            AuditLog::create([
                'user_id' => $this->getAuthUserId(),
                'action' => "Toggled status of keyword '{$kw->keyword}' to {$kw->status}",
                'target_table' => 'keywords',
                'ip_address' => request()->ip()
            ]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => 'Failed to toggle keyword status.'], 500);
        }
    }

    public function deleteKeyword($id)
    {
        $kw = Keyword::findOrFail($id);
        $keywordText = $kw->keyword;
        $kw->delete();

        AuditLog::create([
            'user_id' => $this->getAuthUserId(),
            'action' => "Deleted keyword '{$keywordText}'",
            'target_table' => 'keywords',
            'ip_address' => request()->ip()
        ]);

        return redirect()->route('admin.keywords')->with('info', "Keyword '{$keywordText}' deleted!");
    }

    // --- Settings & Scraper Profiles ---
    public function settings()
    {
        $projectId = $this->getActiveProjectId();
        $accounts = PlatformAccount::where('project_id', $projectId)->get();
        
        // Mock Proxies list
        $proxies = [
            ['ip' => '185.193.18.22', 'port' => '8000', 'location' => 'US - New York', 'status' => 'Active'],
            ['ip' => '45.138.22.109', 'port' => '3128', 'location' => 'DE - Frankfurt', 'status' => 'Active'],
            ['ip' => '103.88.221.14', 'port' => '8080', 'location' => 'SG - Singapore', 'status' => 'Offline'],
        ];

        // Mock Logs list from VM agent
        $vmLogs = [
            ['timestamp' => '2026-06-08 23:45:12', 'level' => 'INFO', 'message' => 'Scraper scheduler initiated successfully.'],
            ['timestamp' => '2026-06-08 23:46:02', 'level' => 'INFO', 'message' => 'Crawling Reddit for keyword: "roast my landing page"'],
            ['timestamp' => '2026-06-08 23:47:15', 'level' => 'SUCCESS', 'message' => 'Discovered 14 posts. Qualified 2 new leads.'],
            ['timestamp' => '2026-06-08 23:55:00', 'level' => 'WARNING', 'message' => 'Proxy 103.88.221.14 timed out. Switched to backup Frankfurt proxy.'],
            ['timestamp' => '2026-06-09 00:01:23', 'level' => 'INFO', 'message' => 'Ollama classification running for post ID t3_1ddxyz1'],
        ];

        return view('admin.settings', compact('accounts', 'proxies', 'vmLogs'));
    }

    public function saveAccount(Request $request)
    {
        $request->validate([
            'platform' => 'required|in:reddit,twitter,linkedin',
            'username' => 'required|string',
            'session_cookies' => 'nullable|string'
        ]);

        $projectId = $this->getActiveProjectId();

        // Safely handle session_cookies — validate JSON if provided
        $cookies = $request->session_cookies;
        if ($cookies) {
            $decoded = json_decode($cookies, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return redirect()->route('admin.settings')->with('info', 'Invalid JSON format for session cookies. Please provide valid JSON.');
            }
            $cookies = $decoded;
        }

        $account = PlatformAccount::updateOrCreate(
            ['project_id' => $projectId, 'platform' => $request->platform],
            [
                'username' => $request->username,
                'session_cookies' => $cookies,
                'status' => 'Active'
            ]
        );

        AuditLog::create([
            'user_id' => $this->getAuthUserId(),
            'action' => "Configured scraper profile for '{$account->platform}' ({$account->username})",
            'target_table' => 'platform_accounts',
            'ip_address' => $request->ip()
        ]);

        return redirect()->route('admin.settings')->with('info', "Scraper profile for {$account->platform} updated!");
    }

    // --- Billing ---
    public function billing()
    {
        $userId = $this->getAuthUserId();
        $subscription = Subscription::where('user_id', $userId)->first();
        $invoices = $subscription ? Invoice::where('subscription_id', $subscription->id)->orderBy('invoice_date', 'desc')->get() : collect();

        // Calculate actual usage stats for quota display
        $activeProjectId = $this->getActiveProjectId();
        $usedLeads = \App\Models\Lead::where('project_id', $activeProjectId)
            ->where('created_at', '>=', now()->startOfMonth())
            ->count();
        $usedKeywords = \App\Models\Keyword::where('status', 'Active')->count();

        return view('admin.billing', compact('subscription', 'invoices', 'usedLeads', 'usedKeywords'));
    }
}
