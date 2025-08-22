<?php

namespace App\Services\Message;

use App\Contracts\MessageRepositoryInterface;

class MessageDestructionService
{
    public function __construct(protected MessageRepositoryInterface $messageRepository)
    {
    }

    /**
     * Delete a message.
     *
     * @param int $messageId
     * @return void
     */
    public function deleteMessage(int $messageId): void
    {
        $this->messageRepository->deleteMessage($messageId);
    }
}
