<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlatformAccount extends Model
{
    protected $fillable = ['project_id', 'platform', 'username', 'session_cookies', 'status', 'last_used_at'];

    protected $casts = [
        'session_cookies' => 'array',
        'last_used_at' => 'datetime'
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
