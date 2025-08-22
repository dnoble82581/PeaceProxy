<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ConversationUser extends Pivot
{
    protected $table = 'conversation_user';

    protected $casts = [
        'joined_at' => 'datetime',
        'left_at' => 'datetime',
    ];

    /**
     * Get the conversation that the user is part of.
     */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    /**
     * Get the user that is part of the conversation.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Determine if the user is still active in the conversation.
     */
    public function isActive(): bool
    {
        return $this->left_at === null;
    }

    /**
     * Mark the user as having left the conversation.
     */
    public function leave(): void
    {
        $this->left_at = now();
        $this->save();
    }

    /**
     * Mark the user as having rejoined the conversation.
     */
    public function rejoin(): void
    {
        $this->left_at = null;
        $this->save();
    }
}
