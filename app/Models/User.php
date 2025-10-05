<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;

/**
 * @property string $name
 * @property string $email
 * @property string $password
 * @property Carbon $email_verified_at
 * @property string $remember_token
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class User extends Authenticatable
{
    use BelongsToTenant;
    use HasFactory;
    use Notifiable;

    protected $guarded = ['id'];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected static function booted(): void
    {
        static::creating(function (User $user): void {
            if (empty($user->tenant_id)) {
                $tenantId = null;

                if (function_exists('tenant') && tenant()) {
                    $tenantId = tenant()->id;
                }

                if (! $tenantId && app()->environment('testing')) {
                    $tenantId = Tenant::factory()->create()->id;
                }

                if ($tenantId) {
                    $user->tenant_id = $tenantId;
                }
            }
        });
    }

    public function primaryTeam()
    {
        // if you added primary_team_id
        return $this->belongsTo(Team::class, 'primary_team_id');
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class)->withTimestamps()->withPivot('is_primary');
    }

    public function activeIncidentAssignment()
    {
        // Implement per your schema; example using a generic table:
        return $this->hasOne(NegotiationUser::class)
            ->whereNull('left_at')
            ->with('negotiation')
            ->latestOfMany();
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function avatarUrl(): string
    {
        if ($this->avatar_path) {
            return $this->avatar_path;
        } else {
            return 'https://ui-avatars.com/api/?name='.$this->name;
        }
    }

    /**
     * The negotiations that this user is involved in.
     */
    public function negotiations()
    {
        return $this->belongsToMany(Negotiation::class, 'negotiation_users')
            ->withPivot(['role', 'status', 'joined_at', 'left_at', 'negotiation_id'])
            ->using(NegotiationUser::class);
    }

    public function isAdmin(): bool
    {
        return $this->permissions === 'admin';
    }

    public function initials(): string
    {
        $nameParts = explode(' ', $this->name);
        $initials = array_map(fn ($part) => $part[0] ?? '', $nameParts);

        return strtoupper(implode('', $initials));
    }

    /**
     * Get the active conversations for this user (those they haven't left).
     */
    public function activeConversations(): BelongsToMany
    {
        return $this->conversations()->wherePivotNull('left_at');
    }

    /**
     * The conversations that this user is part of.
     */
    public function conversations(): BelongsToMany
    {
        return $this->belongsToMany(Conversation::class)
            ->withPivot(['joined_at', 'left_at', 'last_read_at', 'last_read_message_id'])
            ->withTimestamps()
            ->using(ConversationUser::class);
    }

    public function warnings(): HasMany
    {
        return $this->hasMany(Warning::class, 'created_by_id');
    }

    /**
     * Get the conversations created by this user.
     */
    public function createdConversations(): HasMany
    {
        return $this->hasMany(Conversation::class, 'created_by_id');
    }

    /**
     * Get the messages sent by this user.
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function triggers(): HasMany
    {
        return $this->hasMany(Trigger::class, 'created_by_id');
    }

    /**
     * Get the whispers sent to this user.
     */
    public function whispersReceived(): HasMany
    {
        return $this->hasMany(Message::class, 'whisper_to')->where('is_whisper', true);
    }

    public function hooks(): HasMany
    {
        return $this->hasMany(Hook::class, 'created_by');
    }

    /**
     * Get the documents for the user.
     */
    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    /**
     * Get the images for the user.
     */
    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
