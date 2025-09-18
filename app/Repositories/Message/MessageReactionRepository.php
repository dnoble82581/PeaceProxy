<?php

namespace App\Repositories\Message;

use App\Contracts\MessageReactionRepositoryInterface;
use App\Models\MessageReaction;
use Exception;

class MessageReactionRepository implements MessageReactionRepositoryInterface
{
    /**
     * Add a reaction to a message.
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
     * @throws Exception
     */
    public function removeReaction(int $messageReactionId): void
    {
        $messageReaction = MessageReaction::findOrFail($messageReactionId);
        $messageReaction->delete();
    }

    /**
     * Remove a specific reaction type from a message by a user.
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
     */
    public function getMessageReaction(int $messageReactionId): ?MessageReaction
    {
        return MessageReaction::find($messageReactionId);
    }

    public function getMessageReactionCount(int $messageId, string $reactionType)
    {
        $message = app(\App\Services\Message\MessageFetchingService::class)->getMessage($messageId);

        return $message->reactions->where('reaction_type', $reactionType)->count();
    }

    /**
     * Get reactions for a message.
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
     */
    public function hasUserReacted(int $messageId, int $userId, string $reactionType): bool
    {
        return MessageReaction::where('message_id', $messageId)
            ->where('user_id', $userId)
            ->where('reaction_type', $reactionType)
            ->exists();
    }
}
