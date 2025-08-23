<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Call extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $casts = [
        'queued_at' => 'datetime',
        'ringing_at' => 'datetime',
        'answered_at' => 'datetime',
        'completed_at' => 'datetime',
        'last_event_at' => 'datetime',
        'dtmf_payload' => 'array',
        'meta' => 'array',
        'last_event_payload' => 'array',
        // Encrypt sensitive fields:
        'notes' => 'encrypted',
        'transcript_text' => 'encrypted',
        'dtmf_digits' => 'encrypted',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function negotiation(): BelongsTo
    {
        return $this->belongsTo(Negotiation::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(CallEvent::class);
    }

    public function recordings(): HasMany
    {
        return $this->hasMany(CallRecording::class);
    }
}
