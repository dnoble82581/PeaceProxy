<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Message extends Model
{
    use BelongsToTenant;
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'user_id',
        'content',
        'negotiation_id',
        'tenant_id',
        'whisper_to',
        'is_whisper',
    ];

    protected $casts = [
        'is_whisper' => 'boolean',
    ];

    public function logs(): MorphMany
    {
        return $this->morphMany(Log::class, 'loggable');
    }

    public function negotiation()
    {
        return $this->belongsTo(Negotiation::class);
    }

    /**
     * Get the conversation that owns the message.
     */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    /**
     * Get the user that sent the message.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user that the whisper is directed to (if applicable).
     */
    public function whisperRecipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'whisper_to');
    }

    /**
     * Scope a query to only include whispers.
     */
    public function scopeWhispers($query)
    {
        return $query->where('is_whisper', true);
    }

    /**
     * Scope a query to only include regular messages (not whispers).
     */
    public function scopeRegular($query)
    {
        return $query->where('is_whisper', false);
    }

    /**
     * Scope a query to only include whispers to a specific user.
     */
    public function scopeWhispersTo($query, $userId)
    {
        return $query->where('is_whisper', true)
            ->where('whisper_to', $userId);
    }

    /**
     * Determine if the message is visible to a specific user.
     */
    public function isVisibleTo($userId): bool
    {
        // Regular messages are visible to everyone
        if (! $this->is_whisper) {
            return true;
        }

        // Whispers are only visible to the sender and recipient
        return $this->user_id == $userId || $this->whisper_to == $userId;
    }

    /**
     * Get the document attachments for the message.
     */
    public function messageDocuments(): HasMany
    {
        return $this->hasMany(MessageDocument::class);
    }

    /**
     * Get the documents attached to the message.
     */
    public function documents(): HasManyThrough
    {
        return $this->hasManyThrough(Document::class, MessageDocument::class, 'message_id', 'id', 'id', 'document_id');
    }

    /**
     * Get the reactions for the message.
     */
    public function reactions(): HasMany
    {
        return $this->hasMany(MessageReaction::class);
    }

}
