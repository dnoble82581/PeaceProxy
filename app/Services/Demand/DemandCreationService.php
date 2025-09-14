<?php

namespace App\Services\Demand;

use App\Contracts\DemandRepositoryInterface;
use App\DTOs\Demand\DemandDTO;
use App\Events\Demand\DemandCreatedEvent;
use App\Models\Demand;

class DemandCreationService
{
    public function __construct(protected DemandRepositoryInterface $demandRepository)
    {
    }

    public function createDemand(DemandDTO $demandDTO)
    {
        $demand = $this->demandRepository->createDemand($demandDTO->toArray());

        $this->addLogEntry($demand);

        // Dispatch event
        event(new DemandCreatedEvent($demand));

        return $demand;
    }

    private function addLogEntry(Demand $demand): void
    {
        $user = auth()->user();

        app(\App\Services\Log\LogService::class)->writeAsync(
            tenantId: tenant()->id,
            event: 'demand.created',
            headline: "{$user->name} created a demand",
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
