<?php

namespace App\Services\Demand;

use App\Contracts\DemandRepositoryInterface;
use App\DTOs\Demand\DemandDTO;
use App\Events\Demand\DemandUpdatedEvent;
use App\Models\Demand;

class DemandUpdateService
{
    public function __construct(protected DemandRepositoryInterface $demandRepository)
    {
    }

    public function updateDemand($demandId, DemandDTO $demandDTO)
    {
        $demand = $this->demandRepository->updateDemand($demandId, $demandDTO->toArray());

        $this->addLogEntry($demand);

        // Dispatch event
        event(new DemandUpdatedEvent($demand->negotiation_id, $demand->id));

        return $demand;
    }

    private function addLogEntry(Demand $demand): void
    {
        $user = auth()->user();

        app(\App\Services\Log\LogService::class)->writeAsync(
            tenantId: tenant()->id,
            event: 'demand.updated',
            headline: "{$user->name} updated a demand",
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
