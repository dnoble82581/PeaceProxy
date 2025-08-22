<?php

namespace App\Services\Chat;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

/**
 * Service class for handling chat-related operations
 */
class ChatService
{
    /**
     * Get messages for a conversation with proper eager loading
     *
     * @param Conversation|null $conversation The conversation to get messages for
     * @return Collection Empty collection if conversation is null
     */
    public function getMessages(?Conversation $conversation): Collection
    {
        if (!$conversation) {
            return new Collection();
        }

        return $conversation->messages()
            ->with(['user', 'whisperRecipient'])
            ->orderBy('created_at')
            ->get()
            ->filter(function ($message) {
                return $message->isVisibleTo(Auth::id());
            });
    }

    /**
     * Get users participating in a conversation
     *
     * @param Conversation|null $conversation The conversation to get users for
     * @return Collection Empty collection if conversation is null
     */
    public function getConversationUsers(?Conversation $conversation): Collection
    {
        if (!$conversation) {
            return new Collection();
        }

        return $conversation->activeUsers()->get();
    }

    /**
     * Get available users for creating new conversations
     *
     * @param int $tenantId The tenant ID to filter users by
     * @return Collection Collection of users
     */
    public function getAvailableUsers(int $tenantId): Collection
    {
        return User::where('tenant_id', $tenantId)
            ->where('id', '!=', Auth::id())
            ->get();
    }

    /**
     * Get the other user in a private conversation
     *
     * @param Conversation $conversation The private conversation
     * @param int $currentUserId The ID of the current user
     * @return User|null The other user or null if not found
     */
    public function getOtherUserInPrivateConversation(Conversation $conversation, int $currentUserId): ?User
    {
        if ($conversation->type !== 'private') {
            return null;
        }

        return $conversation->activeUsers()
            ->where('users.id', '!=', $currentUserId)
            ->first();
    }

    /**
     * Get the count of active users in a conversation
     *
     * @param Conversation $conversation The conversation
     * @return int The count of active users
     */
    public function getActiveUsersCount(Conversation $conversation): int
    {
        return $conversation->activeUsers()->count();
    }
}
