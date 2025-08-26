<?php

namespace App\Services\Hostage;

use App\Contracts\HostageRepositoryInterface;
use App\DTOs\Hostage\HostageDTO;
use App\Events\Hostage\HostageDestroyedEvent;
use App\Models\Hostage;

class HostageUpdatingService
{
    public function __construct(protected HostageRepositoryInterface $hostageRepository)
    {
    }

    /**
     * Update a hostage using DTO.
     */
    public function updateHostage(HostageDTO $hostageDTO, $hostageId): ?Hostage
    {
        $hostage = $this->hostageRepository->updateHostage($hostageId, $hostageDTO->toArray());
        event(new HostageDestroyedEvent($hostage));
        return $hostage;
    }
}
