<?php

namespace App\Services\Conversation;

use App\Contracts\ConversationRepositoryInterface;
use App\Models\Conversation;

class ConversationFetchingService
{
    public function __construct(protected ConversationRepositoryInterface $conversationRepository)
    {
    }

    /**
     * Get a conversation by ID.
     *
     * @param int $conversationId
     * @return Conversation|null
     */
    public function getConversation(int $conversationId): ?Conversation
    {
        return $this->conversationRepository->getConversation($conversationId);
    }

    /**
     * Get all conversations for a tenant.
     *
     * @param int $tenantId
     * @return array
     */
    public function getConversationsByTenant(int $tenantId): array
    {
        return $this->conversationRepository->getConversationsByTenant($tenantId);
    }

    /**
     * Get all active conversations for a tenant.
     *
     * @param int $tenantId
     * @return array
     */
    public function getActiveConversationsByTenant(int $tenantId): array
    {
        return $this->conversationRepository->getActiveConversationsByTenant($tenantId);
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
        return $this->conversationRepository->getConversationsByType($tenantId, $type);
    }

    /**
     * Get conversations for a user.
     *
     * @param int $userId
     * @return array
     */
    public function getConversationsForUser(int $userId): array
    {
        return $this->conversationRepository->getConversationsForUser($userId);
    }

    /**
     * Get private conversations for the authenticated user in a specific negotiation
     *
     * @param  \App\Models\User  $user  The user to get private conversations for
     * @param  int  $negotiationId  The negotiation ID to get conversations for
     *
     * @return \Illuminate\Database\Eloquent\Collection Collection of private conversations
     */
    public function getPrivateConversations(\App\Models\User $user, int $negotiationId): \Illuminate\Database\Eloquent\Collection
    {
        $conversations = $this->conversationRepository->getPrivateConversationsForUserInNegotiation($user->id, $negotiationId);
        return \App\Models\Conversation::hydrate($conversations);
    }

    /**
     * Get group conversations for the authenticated user in a specific negotiation
     *
     * @param  \App\Models\User  $user  The user to get group conversations for
     * @param  int  $negotiationId  The negotiation ID to get conversations for
     *
     * @return \Illuminate\Database\Eloquent\Collection Collection of group conversations
     */
    public function getGroupConversations(\App\Models\User $user, int $negotiationId): \Illuminate\Database\Eloquent\Collection
    {
        $conversations = $this->conversationRepository->getGroupConversationsForUserInNegotiation($user->id, $negotiationId);
        return \App\Models\Conversation::hydrate($conversations);
    }

    /**
     * Get a conversation by ID, ensuring it belongs to the specified negotiation
     *
     * @param  int|null  $conversationId  The ID of the conversation to get
     * @param  int  $negotiationId  The negotiation ID the conversation should belong to
     *
     * @return Conversation|null The conversation or null if not found or doesn't belong to the negotiation
     */
    public function getConversationById(?int $conversationId, int $negotiationId): ?Conversation
    {
        if (!$conversationId) {
            return null;
        }

        return $this->conversationRepository->getConversationByIdForNegotiation($conversationId, $negotiationId);
    }

    /**
     * Get the public conversation for a tenant and negotiation
     *
     * @param  int  $tenantId  The tenant ID to get the public conversation for
     * @param  int  $negotiationId  The negotiation ID to get the public conversation for
     *
     * @return Conversation|null The public conversation or null if not found
     */
    public function getPublicConversation(int $tenantId, int $negotiationId): ?Conversation
    {
        return $this->conversationRepository->getPublicConversationForTenantAndNegotiation($tenantId, $negotiationId);
    }
}
