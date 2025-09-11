<?php

namespace App\Models;

use App\Enums\General\Genders;
use App\Enums\General\RiskLevels;
use App\Enums\Hostage\HostageInjuryStatus;
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
        'risk_level' => RiskLevels::class,
        'injury_status' => HostageInjuryStatus::class
    ];

    public function negotiation(): BelongsTo
    {
        return $this->belongsTo(Negotiation::class);
    }

    public function contacts(): MorphMany
    {
        return $this->morphMany(ContactPoint::class, 'contactable')->orderBy('created_at', 'desc');
    }

    public function avatarUrl(): string
    {
        // Check for primary image
        $primaryImage = $this->getPrimaryImage();
        if ($primaryImage) {
            return $primaryImage->url;
        }

        // Check for first image
        $firstImage = $this->images()->first();
        if ($firstImage) {
            return $firstImage->url;
        }

        // Fall back to initials
        return 'https://ui-avatars.com/api/?name='.$this->initials();
    }

    public function getPrimaryImage()
    {
        $image = '';
        if ($this->images()->where('is_primary', true)->count() > 0) {
            $image = $this->images()->where('is_primary', true)->first()->url;
        } else {
            $image = 'https://ui-avatars.com/api/?name='.$this->name;
        }

        return $image;
    }

    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function initials(): string
    {
        $nameParts = explode(' ', $this->name);
        $initials = array_map(fn ($part) => $part[0] ?? '', $nameParts);

        return strtoupper(implode('', $initials));
    }

    public function logs(): MorphMany
    {
        return $this->morphMany(Log::class, 'loggable');
    }
}
