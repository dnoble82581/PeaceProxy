<?php

namespace App\Services\Demand;

use App\Contracts\DemandRepositoryInterface;
use App\DTOs\Demand\DemandDTO;
use App\Events\Demand\DemandCreatedEvent;

class DemandCreationService
{
    public function __construct(protected DemandRepositoryInterface $demandRepository)
    {
    }

    public function createDemand(DemandDTO $demandDTO)
    {
        $demand = $this->demandRepository->createDemand($demandDTO->toArray());

        // Dispatch event
        event(new DemandCreatedEvent($demand));

        return $demand;
    }
}
