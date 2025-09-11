<?php

namespace App\Services\Message;

use App\Contracts\MessageRepositoryInterface;
use App\Events\Chat\MessageDeletedEvent;
use App\Models\Message;

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
        $message = $this->messageRepository->getMessage($messageId);

        if (!$message) {
            return;
        }

        $log = $this->addLogEntry($message);
        logger($log);

        // Dispatch event
        event(new MessageDeletedEvent($message));

        $this->messageRepository->deleteMessage($messageId);
    }

    private function addLogEntry(Message $message)
    {
        $user = auth()->user();

        return app(\App\Services\Log\LogService::class)->write(
            tenantId: tenant()->id,
            event: 'message.deleted',
            headline: "{$user->name} deleted a message",
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
