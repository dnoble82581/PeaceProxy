<?php

namespace App\Models;

use App\Enums\General\Genders;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Hostage extends Model
{
    use BelongsToTenant;
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'last_seen_at' => 'datetime',
        'freed_at' => 'datetime',
        'deceased_at' => 'datetime',
        'risk_factors' => 'array',
        'is_primary_hostage' => 'boolean',
        'gender' => Genders::class,
    ];

    public function negotiation(): BelongsTo
    {
        return $this->belongsTo(Negotiation::class);
    }

    public function getPrimaryImage()
    {
        return $this->images()->where('is_primary', true)->first();
    }

    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function contacts(): MorphMany
    {
        return $this->morphMany(ContactPoint::class, 'contactable')->orderBy('created_at', 'desc');
    }
}
