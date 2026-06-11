<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Post extends Model
{
    protected $fillable = ['project_id', 'platform', 'external_id', 'title', 'content', 'author', 'url', 'status', 'scraped_at', 'image_prompt', 'image_url'];

    protected $casts = [
        'scraped_at' => 'datetime'
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function lead(): HasOne
    {
        return $this->hasOne(Lead::class);
    }
}
