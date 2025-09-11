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
        // Get the hostage before deleting it
        $hostage = $this->hostageRepository->getHostage($id);

        if (!$hostage) {
            return null;
        }

        $log = $this->addLogEntry($hostage);
        logger($log);

        $deletedHostage = $this->hostageRepository->deleteHostage($id);
        event(new HostageDestroyedEvent($hostage));

        return $deletedHostage;
    }

    private function addLogEntry(Hostage $hostage)
    {
        $user = auth()->user();

        return app(\App\Services\Log\LogService::class)->write(
            tenantId: tenant()->id,
            event: 'hostage.deleted',
            headline: "{$user->name} deleted a hostage",
            about: $hostage,      // loggable target
            by: $user,            // actor
            description: str($hostage->name)->limit(140),
            properties: [
                'negotiation_id' => $hostage->negotiation_id,
                'is_primary_hostage' => $hostage->is_primary_hostage,
                'risk_level' => $hostage->risk_level?->value,
                'injury_status' => $hostage->injury_status?->value,
            ],
        );
    }
}
