<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgentLog extends Model
{
    protected $fillable = ['agent_run_id', 'level', 'message'];

    public function run(): BelongsTo
    {
        return $this->belongsTo(AgentRun::class);
    }
}
