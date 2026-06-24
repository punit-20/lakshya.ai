<?php

namespace App\Http\Controllers;

use App\Http\Traits\ActiveProjectTrait;
use App\Models\Project;
use App\Models\Keyword;
use App\Models\AuditLog;
use App\Http\Requests\StoreKeywordRequest;
use App\Enums\KeywordStatus;
use Illuminate\Http\Request;

class KeywordController extends Controller
{
    use ActiveProjectTrait;

    public function keywords()
    {
        $projectId = $this->getActiveProjectId();
        $project = Project::findOrFail($projectId);
        $keywords = Keyword::where('project_id', $projectId)->get();

        return view('admin.keywords', compact('project', 'keywords'));
    }

    public function storeKeyword(StoreKeywordRequest $request)
    {
        $projectId = $this->getActiveProjectId();
        
        $kw = Keyword::create([
            'project_id' => $projectId,
            'keyword' => $request->keyword,
            'status' => KeywordStatus::Active->value
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
            $kw->status = $kw->status == KeywordStatus::Active->value ? KeywordStatus::Paused->value : KeywordStatus::Active->value;
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
}
