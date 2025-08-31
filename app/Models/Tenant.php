<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Laravel\Cashier\Billable;

class Tenant extends Model
{
    use Billable;
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'trial_ends_at' => 'datetime',
    ];

    public function billingOwner(): BelongsTo  // optional helper
    {
        return $this->belongsTo(User::class, 'billing_owner_id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function negotiations(): HasMany
    {
        return $this->hasMany(Negotiation::class);
    }

    /**
     * Get the images for the tenant.
     */
    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }
}
