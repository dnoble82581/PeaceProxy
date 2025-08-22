<?php

namespace App\Contracts;

use App\Models\Conversation;

interface ConversationRepositoryInterface
{
    /**
     * Create a new conversation.
     *
     * @param array $data
     * @return Conversation
     */
    public function createConversation(array $data): Conversation;

    /**
     * Delete a conversation.
     *
     * @param int $conversationId
     * @return void
     */
    public function deleteConversation(int $conversationId): void;

    /**
     * Get a conversation by ID.
     *
     * @param int $conversationId
     * @return Conversation|null
     */
    public function getConversation(int $conversationId): ?Conversation;

    /**
     * Get all conversations for a tenant.
     *
     * @param int $tenantId
     * @return array
     */
    public function getConversationsByTenant(int $tenantId): array;

    /**
     * Get all active conversations for a tenant.
     *
     * @param int $tenantId
     * @return array
     */
    public function getActiveConversationsByTenant(int $tenantId): array;

    /**
     * Get conversations by type for a tenant.
     *
     * @param int $tenantId
     * @param string $type
     * @return array
     */
    public function getConversationsByType(int $tenantId, string $type): array;

    /**
     * Get conversations for a user.
     *
     * @param int $userId
     * @return array
     */
    public function getConversationsForUser(int $userId): array;

    /**
     * Get private conversations for a user in a specific negotiation.
     *
     * @param int $userId
     * @param int $negotiationId
     * @return array
     */
    public function getPrivateConversationsForUserInNegotiation(int $userId, int $negotiationId): array;

    /**
     * Get group conversations for a user in a specific negotiation.
     *
     * @param int $userId
     * @param int $negotiationId
     * @return array
     */
    public function getGroupConversationsForUserInNegotiation(int $userId, int $negotiationId): array;

    /**
     * Get a conversation by ID, ensuring it belongs to the specified negotiation.
     *
     * @param int $conversationId
     * @param int $negotiationId
     * @return Conversation|null
     */
    public function getConversationByIdForNegotiation(int $conversationId, int $negotiationId): ?Conversation;

    /**
     * Get the public conversation for a tenant and negotiation.
     *
     * @param int $tenantId
     * @param int $negotiationId
     * @return Conversation|null
     */
    public function getPublicConversationForTenantAndNegotiation(int $tenantId, int $negotiationId): ?Conversation;

    /**
     * Close a conversation (mark as inactive).
     *
     * @param int $conversationId
     * @param int $userId
     * @return bool
     */
    public function closeConversation(int $conversationId, int $userId): bool;
}
