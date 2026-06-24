<?php

namespace App\Http\Controllers;

use App\Http\Traits\ActiveProjectTrait;
use App\Models\Project;
use App\Models\AuditLog;
use App\Http\Requests\StoreProjectRequest;
use App\Enums\ProjectStatus;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    use ActiveProjectTrait;

    public function projects()
    {
        $projects = Project::withCount(['leads', 'keywords'])->get();
        return view('admin.projects', compact('projects'));
    }

    public function storeProject(StoreProjectRequest $request)
    {
        $project = Project::create([
            'user_id' => $this->getAuthUserId(),
            'name' => $request->name,
            'description' => $request->description,
            'status' => ProjectStatus::Active->value
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
        $project->status = $project->status == ProjectStatus::Active->value ? ProjectStatus::Paused->value : ProjectStatus::Active->value;
        $project->save();

        AuditLog::create([
            'user_id' => $this->getAuthUserId(),
            'action' => "Toggled status of project '{$project->name}' to {$project->status}",
            'target_table' => 'projects',
            'ip_address' => request()->ip()
        ]);

        return redirect()->route('admin.projects')->with('info', "Project '{$project->name}' status updated to {$project->status}.");
    }
}
