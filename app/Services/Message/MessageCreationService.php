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
        $log = $this->addLogEntry($newMessage);
        logger($log);
        $this->broadCastMessageCreated($newMessage);

        return $newMessage;
    }

    private function addLogEntry(Message $message)
    {
        $user = auth()->user();

        return app(\App\Services\Log\LogService::class)->write(
            tenantId: tenant()->id,
            event: 'message.sent',
            headline: "{$user->name} sent a message",
            about: $message,      // loggable target
            by: $user,            // actor
            description: str($message->content)->limit(140),
            properties: [
                'conversation_id' => $message->conversation_id,
                'length' => strlen($message->content),
                //				'attachments' => collect($message->attachments)->pluck('id'),
            ],
        );
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
