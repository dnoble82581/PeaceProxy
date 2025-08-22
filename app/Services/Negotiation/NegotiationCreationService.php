<?php

namespace App\Services\Negotiation;

use App\Contracts\NegotiationRepositoryInterface;

class NegotiationCreationService
{
    public function __construct(protected NegotiationRepositoryInterface $negotiationRepository)
    {
    }

    public function createNegotiation($data)
    {
        return $this->negotiationRepository->createNegotiation($data);
    }
}
