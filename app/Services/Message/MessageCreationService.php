<?php

namespace App\Services\Message;

use App\Contracts\MessageRepositoryInterface;
use App\DTOs\Message\MessageDTO;
use App\Events\Chat\MessageSentEvent;
use App\Models\Message;

class MessageCreationService
{
    public function __construct(protected MessageRepositoryInterface $messageRepository)
    {
    }

    /**
     * Create a new message.
     */
    public function createMessage(MessageDTO $messageDTO): Message
    {
        $newMessage = $this->messageRepository->createMessage($messageDTO->toArray());
        $this->broadCastMessageCreated($newMessage);
        return $newMessage;
    }

    public function broadCastMessageCreated(Message $message): bool
    {
        if ($message) {
            event(new MessageSentEvent($message));

            return true;
        }

        return false;
    }
}
