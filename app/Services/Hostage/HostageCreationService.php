<?php

namespace App\Services\Hostage;

use App\Contracts\HostageRepositoryInterface;
use App\DTOs\Hostage\HostageDTO;
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
        return $this->hostageRepository->createHostage($hostageDTO->toArray());
    }
}
