<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AgentRun extends Model
{
    protected $fillable = ['agent_task_id', 'status', 'started_at', 'completed_at', 'result_data'];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'result_data' => 'array',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(AgentTask::class, 'agent_task_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(AgentLog::class);
    }
}
