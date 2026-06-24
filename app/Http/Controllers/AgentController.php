<?php

namespace App\Http\Controllers;

use App\Http\Traits\ActiveProjectTrait;
use App\Models\Project;
use App\Models\Lead;
use App\Models\AgentTask;
use App\Models\AgentRun;
use App\Models\AgentLog;
use App\Models\VisitorHit;
use App\Models\WhatsappLog;
use App\Models\LinkedinLog;
use App\Models\EmailLog;
use App\Models\AgentMemory;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AgentController extends Controller
{
    use ActiveProjectTrait;

    public function index()
    {
        $projectId = $this->getActiveProjectId();
        $project = Project::findOrFail($projectId);

        // Fetch all leads for manual selectors
        $leads = Lead::where('project_id', $projectId)->get();

        // Fetch visitor traffic
        $visitors = VisitorHit::where('project_id', $projectId)->orderBy('created_at', 'desc')->get();

        // Fetch WhatsApp outreach logs
        $whatsappLogs = WhatsappLog::where('project_id', $projectId)->with('lead')->orderBy('created_at', 'desc')->get();

        // Fetch LinkedIn outreach logs
        $linkedinLogs = LinkedinLog::where('project_id', $projectId)->with('lead')->orderBy('created_at', 'desc')->get();

        // Fetch Email outreach logs
        $emailLogs = EmailLog::orderBy('created_at', 'desc')->get();

        // Fetch Agent Tasks with runs and step logs
        $tasks = AgentTask::where('project_id', $projectId)
            ->with(['runs' => function($q) {
                $q->orderBy('created_at', 'desc');
            }, 'runs.logs'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Compute aggregate stats for dashboard gauges
        $totalRuns = AgentRun::whereHas('task', function($q) use ($projectId) {
            $q->where('project_id', $projectId);
        })->count();

        $successRuns = AgentRun::where('status', 'Success')
            ->whereHas('task', function($q) use ($projectId) {
                $q->where('project_id', $projectId);
            })->count();

        $successRate = $totalRuns > 0 ? round(($successRuns / $totalRuns) * 100) : 100;

        // Fetch agent configuration statuses (defaulting to Active)
        $agents = [
            'LeadHunterAgent' => ['name' => 'Lead Hunter', 'desc' => 'Crawls platforms for target buying signals', 'status' => 'Active', 'metric' => 'Hits: ' . count($visitors)],
            'EmailAgent' => ['name' => 'Cold Emailer', 'desc' => 'Generates outreach copies and automates follow-ups', 'status' => 'Active', 'metric' => 'Sent: ' . count($emailLogs)],
            'WhatsAppAgent' => ['name' => 'WhatsApp Connector', 'desc' => 'Sends customized WhatsApp templates via API', 'status' => 'Active', 'metric' => 'Outbox: ' . count($whatsappLogs)],
            'LinkedInAgent' => ['name' => 'LinkedIn outreach', 'desc' => 'Executes profile visits and connects automatically', 'status' => 'Active', 'metric' => 'Actions: ' . count($linkedinLogs)],
            'SEOAgent' => ['name' => 'SEO Blog Generator', 'desc' => 'Performs keyword audits and drafts optimized blogs', 'status' => 'Idle', 'metric' => 'Blogs: 0'],
            'ContentAgent' => ['name' => 'Calendar Publisher', 'desc' => 'Autonomously schedules 30-day marketing calendars', 'status' => 'Idle', 'metric' => 'Posts: 0'],
            'CompetitorAgent' => ['name' => 'Competitor Monitor', 'desc' => 'Tracks brand mentions and schedules counter campaigns', 'status' => 'Active', 'metric' => 'Signals: 2'],
            'MeetingAgent' => ['name' => 'Scheduler Assistant', 'desc' => 'Identifies reply interest and dispatches Calendly links', 'status' => 'Active', 'metric' => 'Meetings: 1'],
            'AnalyticsAgent' => ['name' => 'ROI Calculator', 'desc' => 'Aggregates P&L metrics and subscription costs', 'status' => 'Active', 'metric' => 'Reports: 24'],
        ];

        foreach ($agents as $type => &$data) {
            $mem = AgentMemory::where('project_id', $projectId)
                ->where('memory_key', "agent_{$type}_status")
                ->first();
            if ($mem) {
                $data['status'] = $mem->memory_value;
            }
        }

        return view('admin.agents', compact(
            'project', 'leads', 'visitors', 'whatsappLogs', 'linkedinLogs', 'emailLogs', 'tasks', 'successRate', 'agents'
        ));
    }

    public function toggleAgent(Request $request)
    {
        $projectId = $this->getActiveProjectId();
        $agentType = $request->input('agent_type');
        $status = $request->input('status', 'Active'); // Active or Idle

        // Update or create config in AgentMemory
        AgentMemory::updateOrCreate(
            ['project_id' => $projectId, 'memory_key' => "agent_{$agentType}_status"],
            ['memory_value' => $status, 'type' => 'system']
        );

        AuditLog::create([
            'user_id' => $this->getAuthUserId(),
            'action' => "Toggled agent '{$agentType}' status to {$status}",
            'target_table' => 'agent_memories',
            'ip_address' => $request->ip()
        ]);

        return response()->json(['success' => true]);
    }

    public function enqueueTask(Request $request)
    {
        $projectId = $this->getActiveProjectId();
        $agentType = $request->input('agent_type');
        $taskName = $request->input('task_name');
        $payload = $request->input('payload', []);

        $task = AgentTask::create([
            'project_id' => $projectId,
            'agent_type' => $agentType,
            'task_name' => $taskName,
            'payload' => $payload,
            'status' => 'Pending'
        ]);

        AuditLog::create([
            'user_id' => $this->getAuthUserId(),
            'action' => "Enqueued task '{$taskName}' for {$agentType}",
            'target_table' => 'agent_tasks',
            'ip_address' => $request->ip()
        ]);

        return response()->json([
            'success' => true,
            'task_id' => $task->id,
            'message' => 'Agent task successfully added to queue!'
        ]);
    }

    public function getVisitorStream()
    {
        $projectId = $this->getActiveProjectId();
        $visitors = VisitorHit::where('project_id', $projectId)->orderBy('created_at', 'desc')->get();
        return response()->json(['success' => true, 'visitors' => $visitors]);
    }

    public function getWhatsAppLogs()
    {
        $projectId = $this->getActiveProjectId();
        $logs = WhatsappLog::where('project_id', $projectId)->with('lead')->orderBy('created_at', 'desc')->get();
        return response()->json(['success' => true, 'logs' => $logs]);
    }

    public function getLinkedInLogs()
    {
        $projectId = $this->getActiveProjectId();
        $logs = LinkedinLog::where('project_id', $projectId)->with('lead')->orderBy('created_at', 'desc')->get();
        return response()->json(['success' => true, 'logs' => $logs]);
    }

    public function getQueueLogs()
    {
        $projectId = $this->getActiveProjectId();
        $tasks = AgentTask::where('project_id', $projectId)
            ->with(['runs' => function($q) {
                $q->orderBy('created_at', 'desc');
            }, 'runs.logs'])
            ->orderBy('created_at', 'desc')
            ->get();
        return response()->json(['success' => true, 'tasks' => $tasks]);
    }
}
