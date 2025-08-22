<?php

namespace App\Services\Demand;

use App\Contracts\DemandRepositoryInterface;
use App\DTOs\Demand\DemandDTO;
use App\Models\Demand;

class DemandFetchingService
{
    public function __construct(protected DemandRepositoryInterface $demandRepository)
    {
    }

    public function getDemandById($demandId)
    {
        return $this->demandRepository->getDemand($demandId);
    }

    public function getAllDemands()
    {
        return $this->demandRepository->getDemands();
    }

    public function getDemandsBySubjectId($subjectId)
    {
        return Demand::where('subject_id', $subjectId)->get();
    }

    public function getDemandsByNegotiationId($negotiationId)
    {
        return Demand::where('negotiation_id', $negotiationId)->get();
    }

    public function getDemandDTO($demandId): ?DemandDTO
    {
        $demand = $this->getDemandById($demandId);

        if (!$demand) {
            return null;
        }

        return DemandDTO::fromArray($demand->toArray());
    }
}
