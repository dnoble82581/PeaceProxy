<?php

namespace App\Contracts;

use App\Models\MessageReaction;

interface MessageReactionRepositoryInterface
{
    /**
     * Add a reaction to a message.
     *
     * @param array $data
     * @return MessageReaction
     */
    public function addReaction(array $data): MessageReaction;

    /**
     * Remove a reaction from a message.
     *
     * @param int $messageReactionId
     * @return void
     */
    public function removeReaction(int $messageReactionId): void;

    /**
     * Remove a specific reaction type from a message by a user.
     *
     * @param int $messageId
     * @param int $userId
     * @param string $reactionType
     * @return void
     */
    public function removeReactionByType(int $messageId, int $userId, string $reactionType): void;

    /**
     * Get a message reaction by ID.
     *
     * @param int $messageReactionId
     * @return MessageReaction|null
     */
    public function getMessageReaction(int $messageReactionId): ?MessageReaction;

    /**
     * Get reactions for a message.
     *
     * @param int $messageId
     * @return array
     */
    public function getReactionsByMessage(int $messageId): array;

    /**
     * Get reactions by a specific user.
     *
     * @param int $userId
     * @return array
     */
    public function getReactionsByUser(int $userId): array;

    /**
     * Check if a user has already reacted to a message with a specific reaction type.
     *
     * @param int $messageId
     * @param int $userId
     * @param string $reactionType
     * @return bool
     */
    public function hasUserReacted(int $messageId, int $userId, string $reactionType): bool;
}
