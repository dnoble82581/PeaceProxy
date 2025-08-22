<?php

namespace App\Services\Hostage;

use App\Contracts\HostageRepositoryInterface;
use App\Models\Hostage;

class HostageDestructionService
{
    public function __construct(protected HostageRepositoryInterface $hostageRepository)
    {
    }

    /**
     * Delete a hostage by ID.
     */
    public function deleteHostage($id): ?Hostage
    {
        return $this->hostageRepository->deleteHostage($id);
    }
}
