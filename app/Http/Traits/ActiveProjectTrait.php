<?php

namespace App\Http\Traits;

use App\Models\Project;
use App\Models\User;

trait ActiveProjectTrait
{
    /**
     * Get the active project ID from session, or default to the first available project.
     */
    protected function getActiveProjectId(): int
    {
        $isImpersonating = session()->has('impersonating_client_id');
        $id = session('active_project_id');
        
        if ($isImpersonating) {
            $clientId = session('impersonating_client_id');
            if ($id) {
                $project = Project::find($id);
                if (!$project || $project->user_id !== $clientId) {
                    $id = null;
                }
            }
            if (!$id) {
                $project = Project::where('user_id', $clientId)->first();
                $id = $project ? $project->id : 1;
                session(['active_project_id' => $id]);
            }
            return $id;
        }

        if ($id) {
            $project = Project::find($id);
            if ($project) {
                $user = User::find($this->getAuthUserId());
                if ($user && $user->role === 'client' && $project->user_id !== $user->id) {
                    $id = null;
                }
            } else {
                $id = null;
            }
        }

        if (!$id) {
            $user = User::find($this->getAuthUserId());
            if ($user && $user->role === 'client') {
                $project = Project::where('user_id', $user->id)->first();
                $id = $project ? $project->id : 1;
            } else {
                $first = Project::first();
                $id = $first ? $first->id : 1;
            }
            session(['active_project_id' => $id]);
        }
        return $id;
    }

    /**
     * Get the authenticated user ID with a fallback for development or simulation.
     */
    protected function getAuthUserId(): int
    {
        if (session()->has('impersonating_client_id')) {
            return (int) session('impersonating_client_id');
        }
        return auth()->id() ?? 1;
    }
}
