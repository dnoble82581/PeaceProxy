<?php

namespace App\Services\Negotiation;

use App\Contracts\NegotiationRepositoryInterface;

class NegotiationDestructionService
{
    public function __construct(protected NegotiationRepositoryInterface $negotiationRepository)
    {
    }

    public function deleteNegotiation($negotiationId): void
    {
        $this->negotiationRepository->deleteNegotiation($negotiationId);
    }
}
