<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model
{
    use BelongsToTenant;
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'created_by',
        'negotiation_id',
        'name',
        'type',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the tenant that owns the conversation.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the user that created the conversation.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the negotiation that this conversation belongs to.
     */
    public function negotiation(): BelongsTo
    {
        return $this->belongsTo(Negotiation::class);
    }

    /**
     * Get the active users in this conversation (those who haven't left).
     */
    public function activeUsers(): BelongsToMany
    {
        return $this->users()->wherePivotNull('left_at');
    }

    /**
     * The users that are in this conversation.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot(['joined_at', 'left_at', 'last_read_at', 'last_read_message_id'])
            ->withTimestamps()
            ->using(ConversationUser::class);
    }

    /**
     * Get the messages for this conversation.
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Scope a query to only include public conversations.
     */
    public function scopePublic($query)
    {
        return $query->where('type', 'public');
    }

    /**
     * Scope a query to only include private conversations.
     */
    public function scopePrivate($query)
    {
        return $query->where('type', 'private');
    }

    /**
     * Scope a query to only include group conversations.
     */
    public function scopeGroup($query)
    {
        return $query->where('type', 'group');
    }

    /**
     * Scope a query to only include active conversations.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
