<?php

namespace App\Services\RequestForInformationRecipient;

use App\Contracts\RequestForInformationRecipientRepositoryInterface;
use App\Models\RequestForInformationRecipient;

class RequestForInformationRecipientDestructionService
{
    public function __construct(protected RequestForInformationRecipientRepositoryInterface $recipientRepository)
    {
    }

    public function deleteRecipient($recipientId)
    {
        $recipient = $this->recipientRepository->getRecipient($recipientId);

        if (!$recipient) {
            return null;
        }

        $this->addLogEntry($recipient);

        // No specific event for recipient deletion - this is handled by the UI

        return $this->recipientRepository->deleteRecipient($recipientId);
    }

    private function addLogEntry(RequestForInformationRecipient $recipient): void
    {
        $user = auth()->user();

        app(\App\Services\Log\LogService::class)->writeAsync(
            tenantId: tenant()->id,
            event: 'rfi.recipient.deleted',
            headline: "{$user->name} removed a recipient from a request for information",
            about: $recipient->rfi,      // loggable target (the RFI, not the recipient)
            by: $user,                   // actor
            description: "Recipient removed",
            properties: [
                'request_for_information_id' => $recipient->request_for_information_id,
                'recipient_id' => $recipient->user_id,
            ],
        );
    }
}
