<?php

namespace App\Services\ConversationUser;

use App\Contracts\ConversationUserRepositoryInterface;
use App\Models\ConversationUser;

class ConversationUserFetchingService
{
    public function __construct(protected ConversationUserRepositoryInterface $conversationUserRepository)
    {
    }

    /**
     * Get a conversation user record.
     *
     * @param int $conversationId
     * @param int $userId
     * @return ConversationUser|null
     */
    public function getConversationUser(int $conversationId, int $userId): ?ConversationUser
    {
        return $this->conversationUserRepository->getConversationUser($conversationId, $userId);
    }

    /**
     * Get all users in a conversation.
     *
     * @param int $conversationId
     * @return array
     */
    public function getUsersInConversation(int $conversationId): array
    {
        return $this->conversationUserRepository->getUsersInConversation($conversationId);
    }

    /**
     * Get all active users in a conversation.
     *
     * @param int $conversationId
     * @return array
     */
    public function getActiveUsersInConversation(int $conversationId): array
    {
        return $this->conversationUserRepository->getActiveUsersInConversation($conversationId);
    }
}
