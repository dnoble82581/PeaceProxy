<?php

namespace App\Models;

use App\Enums\DeliveryPlan\Status as DeliveryPlanStatus;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class DeliveryPlan extends Model
{
    use BelongsToTenant;
    use HasFactory;

    protected $guarded = ['id'];

    public function negotiation(): BelongsTo
    {
        return $this->belongsTo(Negotiation::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function documents(): MorphToMany
    {
        return $this->morphToMany(Document::class, 'documentable')->withTimestamps();
    }

    public function planables(): MorphToMany
    {
        // This returns base Model instances; usually you’ll fetch by specific types below.
        return $this->morphedByMany(Model::class, 'planable', 'delivery_planables');
    }

    public function hostages(): MorphToMany
    {
        return $this->morphedByMany(Hostage::class, 'planable', 'delivery_planables')
            ->withPivot(['role', 'notes'])->withTimestamps();
    }

    public function subjects(): MorphToMany
    {
        return $this->morphedByMany(Subject::class, 'planable', 'delivery_planables')
            ->withPivot(['role', 'notes'])->withTimestamps();
    }

    public function demands(): MorphToMany
    {
        return $this->morphedByMany(Demand::class, 'planable', 'delivery_planables')
            ->withPivot(['role', 'notes'])->withTimestamps();
    }

    protected function casts(): array
    {
        return [
            'status' => DeliveryPlanStatus::class,
            'scheduled_at' => 'datetime',
            'window_starts_at' => 'string',  // Properly cast time fields as string
            'window_ends_at' => 'string',    // Properly cast time fields as string
            'geo' => 'array',
            'route' => 'array',
            'instructions' => 'array',
            'constraints' => 'array',
            'contingencies' => 'array',
            'risk_assessment' => 'array',
            'signals' => 'array',
        ];
    }
}
