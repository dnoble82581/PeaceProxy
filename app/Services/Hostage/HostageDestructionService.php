<?php

namespace App\Services\Hostage;

use App\Contracts\HostageRepositoryInterface;
use App\Events\Hostage\HostageDestroyedEvent;
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
        $hostage = $this->hostageRepository->deleteHostage($id);
        event(new HostageDestroyedEvent($hostage));

        return $hostage;
    }
}
