<?php

namespace App\Services\RequestForInformation;

use App\Contracts\RequestForInformationRepositoryInterface;
use App\DTOs\RequestForInformation\RequestForInformationDTO;
use App\Models\RequestForInformation;

class RequestForInformationFetchingService
{
    public function __construct(protected RequestForInformationRepositoryInterface $rfiRepository)
    {
    }

    public function getRfiById($rfiId)
    {
        return $this->rfiRepository->getRfi($rfiId);
    }

    public function getAllRfis()
    {
        return $this->rfiRepository->getRfis();
    }

    public function getRfisByNegotiationId($negotiationId)
    {
        return RequestForInformation::where('negotiation_id', $negotiationId)->get();
    }

    public function getRfisBySenderId($userId)
    {
        return RequestForInformation::where('user_id', $userId)->get();
    }

    public function getRfiDTO($rfiId): ?RequestForInformationDTO
    {
        $rfi = $this->getRfiById($rfiId);

        if (!$rfi) {
            return null;
        }

        return RequestForInformationDTO::fromArray($rfi->toArray());
    }
}
