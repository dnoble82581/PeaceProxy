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

        $this->addLogEntry($demand);

        $data = [
            'negotiationId' => $demand->negotiation_id,
            'id' => $demand->id,
            'category' => $demand->category,
            'status' => $demand->status,
            'created_by_id' => $demand->created_by_id,
            'title' => $demand->title,
            'actorId' => auth()->user()->id,
        ];

        // Dispatch event
        event(new DemandDestroyedEvent($data));

        return $this->demandRepository->deleteDemand($demandId);
    }

    private function addLogEntry(Demand $demand): void
    {
        $user = auth()->user();

        app(\App\Services\Log\LogService::class)->writeAsync(
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
