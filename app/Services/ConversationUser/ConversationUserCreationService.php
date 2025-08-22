<?php

namespace App\Services\ConversationUser;

use App\Contracts\ConversationUserRepositoryInterface;
use App\DTOs\ConversationUser\ConversationUserDTO;
use App\Models\ConversationUser;

class ConversationUserCreationService
{
    public function __construct(protected ConversationUserRepositoryInterface $conversationUserRepository)
    {
    }

    /**
     * Add a user to a conversation.
     *
     * @param ConversationUserDTO $conversationUserDTO
     * @return ConversationUser
     */
    public function addUserToConversation(ConversationUserDTO $conversationUserDTO): ConversationUser
    {
        return $this->conversationUserRepository->addUserToConversation($conversationUserDTO->toArray());
    }
}
