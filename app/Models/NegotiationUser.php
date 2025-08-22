<?php

namespace App\Models;

use App\Enums\User\UserNegotiationRole;
use App\Enums\User\UserNegotiationStatuses;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class NegotiationUser extends Pivot
{
    protected $table = 'negotiation_users';

    protected $guarded = ['id'];

    protected $casts = [
        'status' => UserNegotiationStatuses::class,
        'joined_at' => 'datetime',
        'left_at' => 'datetime',
        'role' => UserNegotiationRole::class,
    ];

    /**
     * The negotiation that this record belongs to.
     */
    public function negotiation(): BelongsTo
    {
        return $this->belongsTo(Negotiation::class);
    }

    /**
     * The user that this record belongs to.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
