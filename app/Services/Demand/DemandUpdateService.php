<?php

namespace App\Services\Demand;

use App\Contracts\DemandRepositoryInterface;
use App\DTOs\Demand\DemandDTO;
use App\Events\Demand\DemandUpdatedEvent;

class DemandUpdateService
{
    public function __construct(protected DemandRepositoryInterface $demandRepository)
    {
    }

    public function updateDemand($demandId, DemandDTO $demandDTO)
    {
        $demand = $this->demandRepository->updateDemand($demandId, $demandDTO->toArray());

        // Dispatch event
        event(new DemandUpdatedEvent($demand));

        return $demand;
    }
}
