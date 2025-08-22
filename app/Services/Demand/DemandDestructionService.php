<?php

namespace App\Services\Demand;

use App\Contracts\DemandRepositoryInterface;
use App\Events\Demand\DemandDestroyedEvent;

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

        // Dispatch event
        event(new DemandDestroyedEvent($demand));

        return $this->demandRepository->deleteDemand($demandId);
    }
}
