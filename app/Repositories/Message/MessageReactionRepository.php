<?php

namespace App\Repositories\Message;

use App\Contracts\MessageReactionRepositoryInterface;
use App\Models\MessageReaction;
use Exception;

class MessageReactionRepository implements MessageReactionRepositoryInterface
{
    /**
     * Add a reaction to a message.
     *
     * @param array $data
     * @return MessageReaction
     */
    public function addReaction(array $data): MessageReaction
    {
        // Check if the reaction already exists to prevent duplicates
        $existing = MessageReaction::where('message_id', $data['message_id'])
            ->where('user_id', $data['user_id'])
            ->where('reaction_type', $data['reaction_type'])
            ->first();

        if ($existing) {
            return $existing;
        }

        return MessageReaction::create($data);
    }

    /**
     * Remove a reaction from a message.
     *
     * @param int $messageReactionId
     * @return void
     * @throws Exception
     */
    public function removeReaction(int $messageReactionId): void
    {
        $messageReaction = MessageReaction::findOrFail($messageReactionId);
        $messageReaction->delete();
    }

    /**
     * Remove a specific reaction type from a message by a user.
     *
     * @param int $messageId
     * @param int $userId
     * @param string $reactionType
     * @return void
     */
    public function removeReactionByType(int $messageId, int $userId, string $reactionType): void
    {
        MessageReaction::where('message_id', $messageId)
            ->where('user_id', $userId)
            ->where('reaction_type', $reactionType)
            ->delete();
    }

    /**
     * Get a message reaction by ID.
     *
     * @param int $messageReactionId
     * @return MessageReaction|null
     */
    public function getMessageReaction(int $messageReactionId): ?MessageReaction
    {
        return MessageReaction::find($messageReactionId);
    }

    /**
     * Get reactions for a message.
     *
     * @param int $messageId
     * @return array
     */
    public function getReactionsByMessage(int $messageId): array
    {
        return MessageReaction::where('message_id', $messageId)
            ->with('user')
            ->get()
            ->toArray();
    }

    /**
     * Get reactions by a specific user.
     *
     * @param int $userId
     * @return array
     */
    public function getReactionsByUser(int $userId): array
    {
        return MessageReaction::where('user_id', $userId)
            ->with('message')
            ->get()
            ->toArray();
    }

    /**
     * Check if a user has already reacted to a message with a specific reaction type.
     *
     * @param int $messageId
     * @param int $userId
     * @param string $reactionType
     * @return bool
     */
    public function hasUserReacted(int $messageId, int $userId, string $reactionType): bool
    {
        return MessageReaction::where('message_id', $messageId)
            ->where('user_id', $userId)
            ->where('reaction_type', $reactionType)
            ->exists();
    }
}
