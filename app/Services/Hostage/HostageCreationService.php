<?php

namespace App\Services\Hostage;

use App\Contracts\HostageRepositoryInterface;
use App\DTOs\Hostage\HostageDTO;
use App\Events\Hostage\HostageCreatedEvent;
use App\Models\Hostage;

class HostageCreationService
{
    public function __construct(protected HostageRepositoryInterface $hostageRepository)
    {
    }

    /**
     * Create a new hostage using DTO.
     */
    public function createHostage(HostageDTO $hostageDTO): Hostage
    {
        $hostage = $this->hostageRepository->createHostage($hostageDTO->toArray());
        event(new HostageCreatedEvent($hostage));
        return $hostage;
    }
}
