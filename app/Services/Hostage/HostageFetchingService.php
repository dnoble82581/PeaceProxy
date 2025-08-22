<?php

namespace App\Services\Hostage;

use App\Contracts\HostageRepositoryInterface;
use App\Models\Hostage;
use Illuminate\Database\Eloquent\Collection;

class HostageFetchingService
{
    public function __construct(protected HostageRepositoryInterface $hostageRepository)
    {
    }

    /**
     * Get a specific hostage by ID.
     */
    public function getHostage($id): ?Hostage
    {
        return $this->hostageRepository->getHostage($id);
    }

    /**
     * Get all hostages.
     */
    public function getHostages(): Collection
    {
        return $this->hostageRepository->getHostages();
    }

    /**
     * Get all hostages for a specific negotiation.
     */
    public function getHostagesByNegotiation($negotiationId): Collection
    {
        return $this->hostageRepository->getHostagesByNegotiation($negotiationId);
    }
}
