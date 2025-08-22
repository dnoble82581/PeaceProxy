<?php

namespace App\Repositories\Conversation;

use App\Contracts\ConversationRepositoryInterface;
use App\Models\Conversation;
use Exception;

class ConversationRepository implements ConversationRepositoryInterface
{
    /**
     * Create a new conversation.
     *
     * @param array $data
     * @return Conversation
     */
    public function createConversation(array $data): Conversation
    {
        return Conversation::create($data);
    }

    /**
     * Delete a conversation.
     *
     * @param int $conversationId
     * @return void
     * @throws Exception
     */
    public function deleteConversation(int $conversationId): void
    {
        $conversation = Conversation::findOrFail($conversationId);
        $conversation->delete();
    }

    /**
     * Get a conversation by ID.
     *
     * @param int $conversationId
     * @return Conversation|null
     */
    public function getConversation(int $conversationId): ?Conversation
    {
        return Conversation::find($conversationId);
    }

    /**
     * Get all conversations for a tenant.
     *
     * @param int $tenantId
     * @return array
     */
    public function getConversationsByTenant(int $tenantId): array
    {
        return Conversation::where('tenant_id', $tenantId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
    }

    /**
     * Get all active conversations for a tenant.
     *
     * @param int $tenantId
     * @return array
     */
    public function getActiveConversationsByTenant(int $tenantId): array
    {
        return Conversation::where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
    }

    /**
     * Get conversations by type for a tenant.
     *
     * @param int $tenantId
     * @param string $type
     * @return array
     */
    public function getConversationsByType(int $tenantId, string $type): array
    {
        return Conversation::where('tenant_id', $tenantId)
            ->where('type', $type)
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
    }

    /**
     * Get conversations for a user.
     *
     * @param int $userId
     * @return array
     */
    public function getConversationsForUser(int $userId): array
    {
        return Conversation::whereHas('users', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
    }

    /**
     * Get private conversations for a user in a specific negotiation.
     *
     * @param int $userId
     * @param int $negotiationId
     * @return array
     */
    public function getPrivateConversationsForUserInNegotiation(int $userId, int $negotiationId): array
    {
        return Conversation::whereHas('users', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
            ->where('type', 'private')
            ->where('is_active', true)
            ->where('negotiation_id', $negotiationId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
    }

    /**
     * Get group conversations for a user in a specific negotiation.
     *
     * @param int $userId
     * @param int $negotiationId
     * @return array
     */
    public function getGroupConversationsForUserInNegotiation(int $userId, int $negotiationId): array
    {
        return Conversation::whereHas('users', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
            ->where('type', 'group')
            ->where('is_active', true)
            ->where('negotiation_id', $negotiationId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
    }

    /**
     * Get a conversation by ID, ensuring it belongs to the specified negotiation.
     *
     * @param int $conversationId
     * @param int $negotiationId
     * @return Conversation|null
     */
    public function getConversationByIdForNegotiation(int $conversationId, int $negotiationId): ?Conversation
    {
        return Conversation::where('id', $conversationId)
            ->where('negotiation_id', $negotiationId)
            ->first();
    }

    /**
     * Get the public conversation for a tenant and negotiation.
     *
     * @param int $tenantId
     * @param int $negotiationId
     * @return Conversation|null
     */
    public function getPublicConversationForTenantAndNegotiation(int $tenantId, int $negotiationId): ?Conversation
    {
        return Conversation::where('tenant_id', $tenantId)
            ->where('negotiation_id', $negotiationId)
            ->where('type', 'public')
            ->where('is_active', true)
            ->first();
    }

    /**
     * Close a conversation (mark as inactive).
     *
     * @param int $conversationId
     * @param int $userId
     * @return bool
     */
    public function closeConversation(int $conversationId, int $userId): bool
    {
        $conversation = Conversation::find($conversationId);

        if (!$conversation || $conversation->created_by !== $userId) {
            return false;
        }

        $conversation->update(['is_active' => false]);
        return true;
    }
}
