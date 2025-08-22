<?php

namespace App\Models;

use App\Enums\Subject\SubjectNegotiationRoles;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class NegotiationSubject extends Pivot
{
    protected $table = 'negotiation_subjects';

    protected $guarded = ['id'];

    protected $casts = [
        'role' => SubjectNegotiationRoles::class,
    ];

    /**
     * The negotiation that this record belongs to.
     */
    public function negotiation(): BelongsTo
    {
        return $this->belongsTo(Negotiation::class);
    }

    /**
     * The subject that this record belongs to.
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }
}
