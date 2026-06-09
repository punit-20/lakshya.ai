<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgentMemory extends Model
{
    protected $table = 'agent_memories';

    protected $fillable = ['project_id', 'memory_key', 'memory_value', 'type'];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
