<?php

namespace App\Services\Negotiation;

use App\Contracts\NegotiationRepositoryInterface;
use App\Models\Negotiation;

class NegotiationFetchingService
{
    public function __construct(protected NegotiationRepositoryInterface $negotiationRepository)
    {
    }

    public function getNegotiationById($negotiationId)
    {
        return $this->negotiationRepository->getNegotiation($negotiationId);
    }

    public function fetchByTenant($tenantId)
    {
        // This method is kept for backward compatibility
        // In a real-world scenario, you might want to add this method to the repository interface
        // For now, we'll implement it directly using the model
        return Negotiation::where('tenant_id', $tenantId)
            ->with('creator')
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
