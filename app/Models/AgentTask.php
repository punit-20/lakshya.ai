<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AgentTask extends Model
{
    protected $fillable = ['project_id', 'agent_type', 'task_name', 'payload', 'status'];

    protected $casts = [
        'payload' => 'array',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function runs(): HasMany
    {
        return $this->hasMany(AgentRun::class);
    }
}
