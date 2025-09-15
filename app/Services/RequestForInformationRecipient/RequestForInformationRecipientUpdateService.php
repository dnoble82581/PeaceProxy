<?php

namespace App\Services\RequestForInformationRecipient;

use App\Contracts\RequestForInformationRecipientRepositoryInterface;
use App\DTOs\RequestForInformationRecipient\RequestForInformationRecipientDTO;
use App\Events\Rfi\RfiReadUpdatedEvent;
use App\Models\RequestForInformationRecipient;
use Carbon\Carbon;

class RequestForInformationRecipientUpdateService
{
    public function __construct(protected RequestForInformationRecipientRepositoryInterface $recipientRepository)
    {
    }

    public function updateRecipient($recipientId, RequestForInformationRecipientDTO $recipientDTO)
    {
        $recipient = $this->recipientRepository->updateRecipient($recipientId, $recipientDTO->toArray());

        $this->addLogEntry($recipient, 'updated');

        return $recipient;
    }

    public function updateReadStatus($recipientId, bool $isRead)
    {
        $recipient = $this->recipientRepository->getRecipient($recipientId);

        if (!$recipient) {
            return null;
        }

        // Update read status and read_at timestamp if marking as read
        $data = ['is_read' => $isRead];
        if ($isRead) {
            $data['read_at'] = Carbon::now();
        }

        $recipient = $this->recipientRepository->updateRecipient($recipientId, $data);

        $this->addLogEntry($recipient, 'read status updated');

        // Dispatch event for read status update
        event(new RfiReadUpdatedEvent($recipient));

        return $recipient;
    }

    private function addLogEntry(RequestForInformationRecipient $recipient, string $action): void
    {
        $user = auth()->user();

        app(\App\Services\Log\LogService::class)->writeAsync(
            tenantId: tenant()->id,
            event: 'rfi.recipient.' . str_replace(' ', '_', $action),
            headline: "{$user->name} {$action} a recipient for a request for information",
            about: $recipient->rfi,      // loggable target (the RFI, not the recipient)
            by: $user,                   // actor
            description: "Recipient {$action}",
            properties: [
                'request_for_information_id' => $recipient->request_for_information_id,
                'recipient_id' => $recipient->user_id,
                'is_read' => $recipient->is_read,
            ],
        );
    }
}
