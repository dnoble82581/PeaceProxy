<?php

namespace App\Services\RequestForInformation;

use App\Contracts\RequestForInformationRepositoryInterface;
use App\DTOs\RequestForInformation\RequestForInformationDTO;
use App\Models\RequestForInformation;
use Illuminate\Support\Collection;

class RequestForInformationFetchingService
{
    public function __construct(protected RequestForInformationRepositoryInterface $rfiRepository)
    {
    }

    public function getAllRfis(): Collection
    {
        return $this->rfiRepository->getRfis();
    }

    public function getRfisByNegotiationId(int $negotiationId): RequestForInformation|Collection
    {
        return RequestForInformation::where('negotiation_id', $negotiationId)->get();
    }

    public function getRfisBySenderId(int $userId): RequestForInformation|Collection
    {
        return RequestForInformation::where('user_id', $userId)->get();
    }

    public function getRfiDTO(int $rfiId): ?RequestForInformationDTO
    {
        $rfi = $this->getRfiById($rfiId);

        if (!$rfi) {
            return null;
        }

        return RequestForInformationDTO::fromArray($rfi->toArray());
    }

    public function getRfiById(int $rfiId): RequestForInformation
    {
        return $this->rfiRepository->getRfi($rfiId);
    }
}
