<?php

namespace App\Services\Conversation;

use App\Contracts\ConversationRepositoryInterface;
use App\Events\Conversation\ConversationClosedEvent;

class ConversationDestructionService
{
    public function __construct(protected ConversationRepositoryInterface $conversationRepository)
    {
    }

    /**
     * Delete a conversation.
     *
     * @param  int  $conversationId
     *
     * @return void
     */
    public function deleteConversation(int $conversationId): void
    {
        $this->conversationRepository->deleteConversation($conversationId);
    }

    /**
     * Close a conversation (mark as inactive)
     *
     * @param  int  $conversationId  The ID of the conversation to close
     * @param  int  $userId  The ID of the user attempting to close the conversation
     *
     * @return bool Whether the conversation was closed successfully
     */
    public function closeConversation(int $conversationId, int $userId): bool
    {
        $result = $this->conversationRepository->closeConversation($conversationId, $userId);

        if ($result) {
            $conversation = $this->conversationRepository->getConversation($conversationId);
            event(new ConversationClosedEvent($conversation));
        }

        return $result;
    }
}
