<?php

namespace App\Services\Message;

use App\Contracts\MessageRepositoryInterface;
use App\Models\Message;

class MessageFetchingService
{
    public function __construct(protected MessageRepositoryInterface $messageRepository)
    {
    }

    /**
     * Get a message by ID.
     *
     * @param int $messageId
     * @return Message|null
     */
    public function getMessage(int $messageId): ?Message
    {
        return $this->messageRepository->getMessage($messageId);
    }

    /**
     * Get messages by conversation ID.
     *
     * @param int $conversationId
     * @return array
     */
    public function getMessagesByConversation(int $conversationId): array
    {
        return $this->messageRepository->getMessagesByConversation($conversationId);
    }

    /**
     * Get whispers for a user in a conversation.
     *
     * @param int $conversationId
     * @param int $userId
     * @return array
     */
    public function getWhispersForUser(int $conversationId, int $userId): array
    {
        return $this->messageRepository->getWhispersForUser($conversationId, $userId);
    }
}
