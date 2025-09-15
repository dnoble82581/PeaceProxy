<?php

namespace App\Services\RequestForInformationRecipient;

use App\Contracts\RequestForInformationRecipientRepositoryInterface;
use App\DTOs\RequestForInformationRecipient\RequestForInformationRecipientDTO;
use App\Models\RequestForInformationRecipient;

class RequestForInformationRecipientCreationService
{
    public function __construct(protected RequestForInformationRecipientRepositoryInterface $recipientRepository)
    {
    }

    public function createRecipient(RequestForInformationRecipientDTO $recipientDTO)
    {
        $recipient = $this->recipientRepository->createRecipient($recipientDTO->toArray());

        $this->addLogEntry($recipient);

        // No specific event for recipient creation - this is handled by the RFI creation event

        return $recipient;
    }

    private function addLogEntry(RequestForInformationRecipient $recipient): void
    {
        $user = auth()->user();

        app(\App\Services\Log\LogService::class)->writeAsync(
            tenantId: tenant()->id,
            event: 'rfi.recipient.created',
            headline: "{$user->name} added a recipient to a request for information",
            about: $recipient->rfi,      // loggable target (the RFI, not the recipient)
            by: $user,                   // actor
            description: "Added recipient to RFI",
            properties: [
                'request_for_information_id' => $recipient->request_for_information_id,
                'recipient_id' => $recipient->user_id,
            ],
        );
    }
}
