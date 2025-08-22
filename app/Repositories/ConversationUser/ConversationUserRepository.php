<?php

namespace App\Repositories\ConversationUser;

use App\Contracts\ConversationUserRepositoryInterface;
use App\Models\Conversation;
use App\Models\ConversationUser;
use App\Models\User;
use Exception;

class ConversationUserRepository implements ConversationUserRepositoryInterface
{
    /**
     * Add a user to a conversation.
     *
     * @param  array  $data
     *
     * @return ConversationUser
     */
    public function addUserToConversation(array $data): ConversationUser
    {
        $conversation = Conversation::findOrFail($data['conversation_id']);
        $user = User::findOrFail($data['user_id']);

        // Check if the user is already in the conversation
        $existingRecord = $this->getConversationUser($data['conversation_id'], $data['user_id']);

        if ($existingRecord) {
            // If the user previously left, mark them as rejoined
            if ($existingRecord->left_at !== null) {
                $this->markUserRejoinedConversation($data['conversation_id'], $data['user_id']);
                return $this->getConversationUser($data['conversation_id'], $data['user_id']);
            }

            return $existingRecord;
        }

        // Add the user to the conversation
        $pivotData = [
            'joined_at' => now(),
        ];

        $conversation->users()->attach($user->id, $pivotData);

        return $this->getConversationUser($data['conversation_id'], $data['user_id']);
    }

    /**
     * Get a conversation user record.
     *
     * @param  int  $conversationId
     * @param  int  $userId
     *
     * @return ConversationUser|null
     */
    public function getConversationUser(int $conversationId, int $userId): ?ConversationUser
    {
        return ConversationUser::where('conversation_id', $conversationId)
            ->where('user_id', $userId)
            ->first();
    }

    /**
     * Mark a user as having rejoined a conversation.
     *
     * @param  int  $conversationId
     * @param  int  $userId
     *
     * @return void
     */
    public function markUserRejoinedConversation(int $conversationId, int $userId): void
    {
        $conversationUser = $this->getConversationUser($conversationId, $userId);

        if ($conversationUser) {
            $conversationUser->rejoin();
        }
    }

    /**
     * Remove a user from a conversation.
     *
     * @param  int  $conversationId
     * @param  int  $userId
     *
     * @return void
     * @throws Exception
     */
    public function removeUserFromConversation(int $conversationId, int $userId): void
    {
        $conversation = Conversation::findOrFail($conversationId);
        $conversation->users()->detach($userId);
    }

    /**
     * Get all users in a conversation.
     *
     * @param  int  $conversationId
     *
     * @return array
     */
    public function getUsersInConversation(int $conversationId): array
    {
        $conversation = Conversation::findOrFail($conversationId);
        return $conversation->users()->get()->toArray();
    }

    /**
     * Get all active users in a conversation.
     *
     * @param  int  $conversationId
     *
     * @return array
     */
    public function getActiveUsersInConversation(int $conversationId): array
    {
        $conversation = Conversation::findOrFail($conversationId);
        return $conversation->activeUsers()->get()->toArray();
    }

    /**
     * Mark a user as having left a conversation.
     *
     * @param  int  $conversationId
     * @param  int  $userId
     *
     * @return void
     */
    public function markUserLeftConversation(int $conversationId, int $userId): void
    {
        $conversationUser = $this->getConversationUser($conversationId, $userId);

        if ($conversationUser) {
            $conversationUser->leave();
        }
    }
}
