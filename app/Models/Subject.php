<?php

namespace App\Models;

use App\Enums\Subject\MoodLevels;
use App\Enums\Subject\SubjectNegotiationStatuses;
use App\Traits\BelongsToTenant;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use PHPUnit\Framework\TestStatus\Warning;
use Propaganistas\LaravelPhone\Casts\RawPhoneNumberCast;

class Subject extends Model
{
    use BelongsToTenant;
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'date_of_birth' => 'date',
        'current_mood' => MoodLevels::class,
        'status' => SubjectNegotiationStatuses::class,
        'risk_factors' => 'array',
        'phone' => RawPhoneNumberCast::class.':US',
        'alias' => 'array',
    ];

    /**
     * The negotiations that this subject is involved in.
     */
    public function negotiations(): BelongsToMany
    {
        return $this->belongsToMany(Negotiation::class, 'negotiation_subjects')
            ->withPivot('role')
            ->using(NegotiationSubject::class);
    }

    /**
     * Get the URL of the primary image for the subject or a default image.
     * First tries to find a primary image, then any image, then falls back to a temporary URL.
     */
    public function primaryImage(): string
    {
        // Use eager loading to avoid N+1 query issues
        if (! $this->relationLoaded('images')) {
            $this->load('images');
        }

        // First try to find an image marked as primary
        $primaryImage = $this->images->first();

        // If no primary image, use the first image available
        if (! $primaryImage && $this->images->isNotEmpty()) {
            $primaryImage = $this->images->first();
        }

        // Return the image URL or fall back to temporary URL
        if ($primaryImage) {
            // Use the url property if it exists, otherwise call the url() method
            return $primaryImage->url ?? $primaryImage->url();
        }

        return $this->temporaryImageUrl();
    }

    /**
     * Generate a temporary image URL using the hostage's name
     */
    public function temporaryImageUrl(): string
    {
        $fullName = urlencode($this->name);

        return 'https://api.dicebear.com/9.x/initials/svg?seed='.$fullName;
    }

    public function subjectAge(): int
    {
        if ($this->date_of_birth) {
            return Carbon::parse($this->date_of_birth)->age;
        }

        return 0;
    }

    public function riskAssessments(): HasMany
    {
        return $this->hasMany(Assessment::class, 'subject_id');
    }

    public function demands(): HasMany
    {
        return $this->hasMany(Demand::class);
    }

    public function warnings(): HasMany
    {
        return $this->hasMany(Warning::class)->orderBy('created_at', 'desc');
    }

    public function warrants(): HasMany
    {
        return $this->hasMany(Warrant::class);
    }

    public function hooks(): HasMany
    {
        return $this->hasMany(Hook::class);
    }

    public function triggers(): HasMany
    {
        return $this->hasMany(Trigger::class);
    }

    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function moods(): HasMany
    {
        return $this->hasMany(MoodLog::class);
    }

    public function contactPoints(): MorphMany
    {
        return $this->morphMany(ContactPoint::class, 'contactable');
    }

    /**
     * Get the documents for the subject.
     */
    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function riskAssessment(): HasMany
    {
        return $this->hasMany(Assessment::class);
    }
}
