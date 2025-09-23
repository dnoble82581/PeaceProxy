<?php

namespace App\Services\Message;

use App\Contracts\MessageRepositoryInterface;
use App\DTOs\Message\MessageDTO;
use App\Models\Message;

class MessageUpdateService
{
    public function __construct(protected MessageRepositoryInterface $messageRepository)
    {
    }

    /**
     * Update a message.
     *
     * @param int $messageId
     * @param MessageDTO $messageDTO
     * @return Message|null
     */
    public function updateMessage(int $messageId, MessageDTO $messageDTO): ?Message
    {
        $message = $this->messageRepository->updateMessage($messageId, $messageDTO->toArray());

        if (!$message) {
            return null;
        }

        $this->addLogEntry($message);

        // Dispatch event

        return $message;
    }

    private function addLogEntry(Message $message)
    {
        $user = auth()->user();

        return app(\App\Services\Log\LogService::class)->write(
            tenantId: tenant()->id,
            event: 'message.updated',
            headline: "{$user->name} updated a message",
            about: $message,      // loggable target
            by: $user,            // actor
            description: str($message->content)->limit(140),
            properties: [
                'conversation_id' => $message->conversation_id,
                'length' => strlen($message->content),
            ],
        );
    }
}
