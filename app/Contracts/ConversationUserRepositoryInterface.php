<?php

namespace App\Contracts;

use App\Models\ConversationUser;

interface ConversationUserRepositoryInterface
{
    /**
     * Add a user to a conversation.
     *
     * @param  array  $data
     *
     * @return ConversationUser
     */
    public function addUserToConversation(array $data): ConversationUser;

    /**
     * Remove a user from a conversation.
     *
     * @param  int  $conversationId
     * @param  int  $userId
     *
     * @return void
     */
    public function removeUserFromConversation(int $conversationId, int $userId): void;

    /**
     * Get a conversation user record.
     *
     * @param  int  $conversationId
     * @param  int  $userId
     *
     * @return ConversationUser|null
     */
    public function getConversationUser(int $conversationId, int $userId): ?ConversationUser;

    /**
     * Get all users in a conversation.
     *
     * @param  int  $conversationId
     *
     * @return array
     */
    public function getUsersInConversation(int $conversationId): array;

    /**
     * Get all active users in a conversation.
     *
     * @param  int  $conversationId
     *
     * @return array
     */
    public function getActiveUsersInConversation(int $conversationId): array;

    /**
     * Mark a user as having left a conversation.
     *
     * @param  int  $conversationId
     * @param  int  $userId
     *
     * @return void
     */
    public function markUserLeftConversation(int $conversationId, int $userId): void;

    /**
     * Mark a user as having rejoined a conversation.
     *
     * @param  int  $conversationId
     * @param  int  $userId
     *
     * @return void
     */
    public function markUserRejoinedConversation(int $conversationId, int $userId): void;
}
