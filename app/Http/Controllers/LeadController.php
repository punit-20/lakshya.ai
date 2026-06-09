<?php

namespace App\Http\Controllers;

use App\Http\Traits\ActiveProjectTrait;
use App\Models\Lead;
use App\Models\Post;
use App\Models\Conversation;
use App\Models\Meeting;
use App\Models\Notification;
use App\Models\AuditLog;
use App\Models\Project;
use App\Models\AgentMemory;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LeadController extends Controller
{
    use ActiveProjectTrait;

    public function index()
    {
        $projectId = $this->getActiveProjectId();
        
        $statuses = ['New', 'Discovered', 'Contacted', 'Qualified', 'Closed'];
        $leadsByStatus = [];
        
        foreach ($statuses as $status) {
            $leadsByStatus[$status] = Lead::where('project_id', $projectId)
                ->where('status', $status)
                ->orderBy('score', 'desc')
                ->get();
        }

        return view('admin.crm', compact('leadsByStatus', 'statuses'));
    }

    public function updateStatus(Request $request)
    {
        try {
            $request->validate([
                'lead_id' => 'required|exists:leads,id',
                'status' => 'required|in:New,Discovered,Contacted,Qualified,Closed'
            ]);

            $lead = Lead::findOrFail($request->lead_id);
            $oldStatus = $lead->status;
            $lead->status = $request->status;
            $lead->save();

            AuditLog::create([
                'user_id' => $this->getAuthUserId(),
                'action' => "Moved lead '{$lead->contact_name}' from {$oldStatus} to {$request->status}",
                'target_table' => 'leads',
                'ip_address' => $request->ip()
            ]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => 'Failed to update lead status.'], 500);
        }
    }

    public function getDetails($id)
    {
        try {
            $lead = Lead::with(['post', 'conversation', 'meetings'])->findOrFail($id);
            return response()->json([
                'success' => true,
                'lead' => $lead
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => 'Lead not found.'], 404);
        }
    }

    public function generateAiReply(Request $request)
    {
        try {
            $request->validate(['lead_id' => 'required|exists:leads,id']);
            $lead = Lead::with('post')->findOrFail($request->lead_id);
            
            // Fetch memories for custom pitch formulation
            $pitch = AgentMemory::where('project_id', $lead->project_id)->where('memory_key', 'offering_pitch')->first();
            $cta = AgentMemory::where('project_id', $lead->project_id)->where('memory_key', 'call_to_action')->first();

            $pitchText = $pitch ? $pitch->memory_value : "our professional SaaS product solutions";
            $ctaText = $cta ? $cta->memory_value : "visit our website";

            // Null-safe access to post content
            $author = $lead->contact_name;
            $postContent = $lead->post?->content ?? 'your recent post';
            $contentSample = substr($postContent, 0, 80) . "...";

            $reply = "Hey {$author}! I read your post about: \"{$contentSample}\". 

It sounds like you are facing some friction there. Have you checked out our solution? {$pitchText}

We actually offer a quick free evaluation tool to help you get started right away. Feel free to try it here: {$ctaText}

Hope this helps, let me know if you want to chat details!";

            $lead->generated_reply = $reply;
            $lead->save();

            return response()->json([
                'success' => true,
                'reply' => $reply
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => 'Failed to generate AI reply.'], 500);
        }
    }

    public function saveReply(Request $request)
    {
        try {
            $request->validate([
                'lead_id' => 'required|exists:leads,id',
                'reply' => 'required|string'
            ]);

            $lead = Lead::findOrFail($request->lead_id);
            $lead->generated_reply = $request->reply;
            $lead->save();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => 'Failed to save reply draft.'], 500);
        }
    }

    public function sendMessage(Request $request)
    {
        try {
            $request->validate([
                'lead_id' => 'required|exists:leads,id',
                'message' => 'required|string'
            ]);

            $lead = Lead::findOrFail($request->lead_id);
            
            // Find or create conversation
            $conversation = Conversation::firstOrNew([
                'lead_id' => $lead->id,
                'project_id' => $lead->project_id
            ]);

            $messages = $conversation->messages ?? [];
            $messages[] = [
                'sender' => 'agent',
                'message' => $request->message,
                'timestamp' => Carbon::now()->toDateTimeString()
            ];

            $conversation->messages = $messages;
            $conversation->last_message_at = Carbon::now();
            $conversation->save();

            // Update lead status
            $lead->status = 'Contacted';
            $lead->save();

            AuditLog::create([
                'user_id' => $this->getAuthUserId(),
                'action' => "Sent message to lead '{$lead->contact_name}' via " . ($lead->post?->platform ?? 'outreach'),
                'target_table' => 'conversations',
                'ip_address' => $request->ip()
            ]);

            return response()->json([
                'success' => true,
                'conversation' => $conversation
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => 'Failed to send message.'], 500);
        }
    }

    public function scheduleMeeting(Request $request)
    {
        try {
            $request->validate([
                'lead_id' => 'required|exists:leads,id',
                'scheduled_at' => 'required|date',
                'meeting_link' => 'nullable|url'
            ]);

            $lead = Lead::findOrFail($request->lead_id);

            $meeting = Meeting::create([
                'lead_id' => $lead->id,
                'project_id' => $lead->project_id,
                'scheduled_at' => Carbon::parse($request->scheduled_at),
                'duration_minutes' => 30,
                'meeting_link' => $request->meeting_link ?? 'https://meet.google.com/lakshya-session',
                'status' => 'Scheduled'
            ]);

            $lead->status = 'Closed';
            $lead->save();

            // Send system notification
            Notification::create([
                'user_id' => $this->getAuthUserId(),
                'title' => 'Meeting Scheduled!',
                'message' => "Meeting scheduled with {$lead->contact_name} for " . Carbon::parse($request->scheduled_at)->format('M d, H:i A'),
                'is_read' => false
            ]);

            AuditLog::create([
                'user_id' => $this->getAuthUserId(),
                'action' => "Scheduled meeting with lead '{$lead->contact_name}' for " . $request->scheduled_at,
                'target_table' => 'meetings',
                'ip_address' => $request->ip()
            ]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => 'Failed to schedule meeting.'], 500);
        }
    }
}
