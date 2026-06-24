<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhatsappLog extends Model
{
    protected $table = 'whatsapp_logs';

    protected $fillable = ['project_id', 'lead_id', 'phone_number', 'message', 'status', 'reply_message', 'sentiment'];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }
}
