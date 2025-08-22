<?php

namespace App\Services\Objective;

use App\Contracts\ObjectiveRepositoryInterface;
use App\Models\Negotiation;

class ObjectiveFetchingService
{
    protected ObjectiveRepositoryInterface $objectiveRepository;

    public function __construct(ObjectiveRepositoryInterface $objectiveRepository)
    {
        $this->objectiveRepository = $objectiveRepository;
    }

    public function fetchObjectiveById($id)
    {
        return $this->objectiveRepository->getObjective($id);
    }

    public function fetchAllObjectives()
    {
        return $this->objectiveRepository->getObjectives();
    }

    public function fetchObjectivesByNegotiation(Negotiation $negotiation)
    {
        return $this->objectiveRepository->getObjectivesByNegotiation($negotiation->id);
    }

    public function fetchObjectivesByNegotiationId(int $negotiationId)
    {
        return $this->objectiveRepository->getObjectivesByNegotiation($negotiationId);
    }
}
