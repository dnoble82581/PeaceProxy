<?php

namespace App\Contracts;

use App\Models\Message;

interface MessageRepositoryInterface
{
    /**
     * Create a new message.
     *
     * @param array $data
     * @return Message
     */
    public function createMessage(array $data): Message;

    /**
     * Delete a message.
     *
     * @param int $messageId
     * @return void
     */
    public function deleteMessage(int $messageId): void;

    /**
     * Get a message by ID.
     *
     * @param int $messageId
     * @return Message|null
     */
    public function getMessage(int $messageId): ?Message;

    /**
     * Get messages by conversation ID.
     *
     * @param int $conversationId
     * @return array
     */
    public function getMessagesByConversation(int $conversationId): array;

    /**
     * Get whispers for a user in a conversation.
     *
     * @param int $conversationId
     * @param int $userId
     * @return array
     */
    public function getWhispersForUser(int $conversationId, int $userId): array;
}
