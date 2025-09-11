<?php

namespace App\Models;

use App\Enums\Negotiation\NegotiationStatuses;
use App\Enums\Negotiation\NegotiationTypes;
use App\Enums\Subject\SubjectNegotiationRoles;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Negotiation extends Model
{
    use HasFactory;
    use BelongsToTenant;

    protected $guarded = ['id'];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'status' => NegotiationStatuses::class,
        'type' => NegotiationTypes::class,
        'tags' => 'array',
    ];

    /**
     * Get the tenant that owns the negotiation.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * The users that are involved in this negotiation.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'negotiation_users')
            ->withPivot(['role', 'status', 'joined_at', 'left_at'])
            ->using(NegotiationUser::class);
    }

    public function primarySubject()
    {
        return $this->subjects()
            ->wherePivot('role', SubjectNegotiationRoles::primary->value)
            ->first();
    }

    /**
     * The subjects that are involved in this negotiation.
     */
    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'negotiation_subjects')
            ->withPivot('role')
            ->using(NegotiationSubject::class);
    }

    public function hostages()
    {
        return $this->hasMany(Hostage::class);
    }

    /**
     * Get the conversations for this negotiation.
     */
    public function conversations()
    {
        return $this->hasMany(Conversation::class);
    }

    public function hooks(): HasMany
    {
        return $this->hasMany(Hook::class);
    }

    /**
     * Get the documents for the negotiation.
     */
    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function riskAssessments(): HasMany
    {
        return $this->hasMany(AssessmentQuestionResponse::class, 'negotiation_id');
    }

    /**
     * Get the user who created the negotiation.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
