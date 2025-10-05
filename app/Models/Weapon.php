<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Weapon extends Model
{
    use BelongsToTenant;
    use HasFactory;
    use SoftDeletes;

    protected $casts = [
        'last_seen_at' => 'datetime',
        'threat_level' => 'integer',
    ];

    protected $guarded = ['id'];

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    // Simple scopes you’ll actually use

    public function scopeActive($q)
    {
        return $q->where('status', '!=', 'recovered');
    }

    public function scopeLethal($q)
    {
        return $q->whereIn('category', ['handgun', 'rifle', 'shotgun', 'explosive']);
    }

    public function scopeForTenant($q, $tenantId)
    {
        return $q->where('tenant_id', $tenantId);
    }

    protected function casts(): array
    {
        return [
            'last_seen_at' => 'timestamp',
        ];
    }
}
