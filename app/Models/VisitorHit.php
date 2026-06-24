<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VisitorHit extends Model
{
    protected $fillable = ['project_id', 'ip_address', 'company_name', 'pages_visited', 'intent_score'];

    protected $casts = [
        'pages_visited' => 'array',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
