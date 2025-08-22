<?php

namespace App\Repositories\Message;

use App\Contracts\MessageRepositoryInterface;
use App\Models\Message;
use Exception;

class MessageRepository implements MessageRepositoryInterface
{
    /**
     * Create a new message.
     *
     * @param array $data
     * @return Message
     */
    public function createMessage(array $data): Message
    {
        return Message::create($data);
    }

    /**
     * Delete a message.
     *
     * @param int $messageId
     * @return void
     * @throws Exception
     */
    public function deleteMessage(int $messageId): void
    {
        $message = Message::findOrFail($messageId);
        $message->delete();
    }

    /**
     * Get a message by ID.
     *
     * @param int $messageId
     * @return Message|null
     */
    public function getMessage(int $messageId): ?Message
    {
        return Message::find($messageId);
    }

    /**
     * Get messages by conversation ID.
     *
     * @param int $conversationId
     * @return array
     */
    public function getMessagesByConversation(int $conversationId): array
    {
        return Message::where('conversation_id', $conversationId)
            ->orderBy('created_at')
            ->get()
            ->toArray();
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
        return Message::where('conversation_id', $conversationId)
            ->where(function ($query) use ($userId) {
                $query->where('user_id', $userId)
                    ->orWhere('whisper_to', $userId);
            })
            ->where('is_whisper', true)
            ->orderBy('created_at')
            ->get()
            ->toArray();
    }
}
