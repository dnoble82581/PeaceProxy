<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Log extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $fillable = [
        'tenant_id', 'loggable_type', 'loggable_id', 'actor_type', 'actor_id',
        'event', 'channel', 'severity', 'headline', 'description', 'properties',
        'ip_address', 'user_agent', 'occurred_at',
    ];

    protected $casts = [
        'properties' => 'array',
        'occurred_at' => 'datetime',
    ];

    // Immutable-by-convention (we won’t update logs after create)

    public function setUpdatedAt($value)
    { /* no-op to discourage edits */
    }

    public function loggable(): MorphTo
    {
        return $this->morphTo();
    }

    public function actor(): MorphTo
    {
        return $this->morphTo();
    }

    /* Query scopes for reports/timelines */
    public function scopeForTenant($q, $tenantId)
    {
        return $q->where('tenant_id', $tenantId);
    }

    public function scopeBetween($q, $from, $to)
    {
        return $q->whereBetween('occurred_at', [$from, $to]);
    }

    public function scopeEvent($q, $event)
    {
        return $q->where('event', $event);
    }

    public function scopeFor($q, Model $m)
    {
        return $q->whereMorphedTo('loggable', $m);
    }
}
