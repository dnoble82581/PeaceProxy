<?php

namespace App\Services\RequestForInformationRecipient;

use App\Contracts\RequestForInformationRecipientRepositoryInterface;
use App\DTOs\RequestForInformationRecipient\RequestForInformationRecipientDTO;
use App\Models\RequestForInformationRecipient;

class RequestForInformationRecipientFetchingService
{
    public function __construct(protected RequestForInformationRecipientRepositoryInterface $recipientRepository)
    {
    }

    public function getRecipientById($recipientId)
    {
        return $this->recipientRepository->getRecipient($recipientId);
    }

    public function getAllRecipients()
    {
        return $this->recipientRepository->getRecipients();
    }

    public function getRecipientsByRfiId($rfiId)
    {
        return $this->recipientRepository->getRecipientsByRfiId($rfiId);
    }

    public function getRecipientByRfiIdAndUserId($rfiId, $userId)
    {
        return $this->recipientRepository->getRecipientByRfiIdAndUserId($rfiId, $userId);
    }

    public function getRecipientsByUserId($userId)
    {
        return RequestForInformationRecipient::where('user_id', $userId)->get();
    }

    public function getRecipientDTO($recipientId): ?RequestForInformationRecipientDTO
    {
        $recipient = $this->getRecipientById($recipientId);

        if (!$recipient) {
            return null;
        }

        return RequestForInformationRecipientDTO::fromArray($recipient->toArray());
    }
}
