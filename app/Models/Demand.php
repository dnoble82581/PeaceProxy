<?php

namespace App\Models;

use App\Enums\Demand\DemandCategories;
use App\Enums\Demand\DemandStatuses;
use App\Enums\General\Channels;
use App\Enums\General\RiskLevels;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Demand extends Model
{
    use BelongsToTenant;
    use HasFactory;

    protected $guarded = ['id'];

    public function negotiation(): BelongsTo
    {
        return $this->belongsTo(Negotiation::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function deliveryPlans()
    {
        return $this->morphToMany(DeliveryPlan::class, 'planable', 'delivery_planables')
            ->withPivot(['role', 'notes'])->withTimestamps();
    }

    public function logs(): MorphMany
    {
        return $this->morphMany(Log::class, 'loggable');
    }

    protected function casts(): array
    {
        return [
            'communicated_at' => 'datetime',
            'responded_at' => 'datetime',
            'resolved_at' => 'datetime',
            'status' => DemandStatuses::class,
            'category' => DemandCategories::class,
            'priority_level' => RiskLevels::class,
            'channel' => Channels::class,
            'deadline_date' => 'date',
        ];
    }
}
