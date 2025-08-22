<?php

namespace App\Services\Negotiation;

use App\Contracts\NegotiationRepositoryInterface;

class NegotiationService
{
    public function __construct(protected NegotiationRepositoryInterface $negotiationRepository)
    {
    }

    public function createNegotiation($data)
    {
        return $this->negotiationRepository->createNegotiation($data);
    }

    public function deleteNegotiation($negotiationId): void
    {
        $this->negotiationRepository->deleteNegotiation($negotiationId);
    }

    public function getNegotiationById($negotiationId)
    {
        return $this->negotiationRepository->getNegotiation($negotiationId);
    }
}
