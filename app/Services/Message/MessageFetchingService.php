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
     */
    public function getMessage(int $messageId, ?array $with = []): ?Message
    {
        $message = $this->messageRepository->getMessage($messageId);

        if ($message && $with) {
            return $message->load($with);
        }

        return $message;
    }

    /**
     * Get messages by conversation ID.
     */
    public function getMessagesByConversation(int $conversationId): array
    {
        return $this->messageRepository->getMessagesByConversation($conversationId);
    }

    /**
     * Get whispers for a user in a conversation.
     */
    public function getWhispersForUser(int $conversationId, int $userId): array
    {
        return $this->messageRepository->getWhispersForUser($conversationId, $userId);
    }
}
