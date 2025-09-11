<?php

namespace App\Services\Demand;

use App\Contracts\DemandRepositoryInterface;
use App\Events\Demand\DemandDestroyedEvent;
use App\Models\Demand;

class DemandDestructionService
{
    public function __construct(protected DemandRepositoryInterface $demandRepository)
    {
    }

    public function deleteDemand($demandId)
    {
        $demand = $this->demandRepository->getDemand($demandId);

        if (!$demand) {
            return null;
        }

        $log = $this->addLogEntry($demand);
        logger($log);

        // Dispatch event
        event(new DemandDestroyedEvent($demand));

        return $this->demandRepository->deleteDemand($demandId);
    }

    private function addLogEntry(Demand $demand)
    {
        $user = auth()->user();

        return app(\App\Services\Log\LogService::class)->write(
            tenantId: tenant()->id,
            event: 'demand.deleted',
            headline: "{$user->name} deleted a demand",
            about: $demand,      // loggable target
            by: $user,            // actor
            description: str($demand->title)->limit(140),
            properties: [
                'negotiation_id' => $demand->negotiation_id,
                'category' => $demand->category?->value,
                'status' => $demand->status?->value,
            ],
        );
    }
}
