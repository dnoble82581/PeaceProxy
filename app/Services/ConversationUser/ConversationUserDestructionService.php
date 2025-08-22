<?php

namespace App\Services\ConversationUser;

use App\Contracts\ConversationUserRepositoryInterface;

class ConversationUserDestructionService
{
    public function __construct(protected ConversationUserRepositoryInterface $conversationUserRepository)
    {
    }

    /**
     * Remove a user from a conversation.
     *
     * @param int $conversationId
     * @param int $userId
     * @return void
     */
    public function removeUserFromConversation(int $conversationId, int $userId): void
    {
        $this->conversationUserRepository->removeUserFromConversation($conversationId, $userId);
    }

    /**
     * Mark a user as having left a conversation.
     *
     * @param int $conversationId
     * @param int $userId
     * @return void
     */
    public function markUserLeftConversation(int $conversationId, int $userId): void
    {
        $this->conversationUserRepository->markUserLeftConversation($conversationId, $userId);
    }
}
