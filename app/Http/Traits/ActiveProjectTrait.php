<?php

namespace App\Http\Traits;

use App\Models\Project;

trait ActiveProjectTrait
{
    /**
     * Get the active project ID from session, or default to the first available project.
     */
    protected function getActiveProjectId(): int
    {
        $id = session('active_project_id');
        if (!$id) {
            $first = Project::first();
            $id = $first ? $first->id : 1;
            session(['active_project_id' => $id]);
        }
        return $id;
    }

    /**
     * Get the authenticated user ID with a fallback for development.
     */
    protected function getAuthUserId(): int
    {
        return auth()->id() ?? 1;
    }
}
