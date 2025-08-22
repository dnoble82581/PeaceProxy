<?php

namespace App\Services\Objective;

use App\Contracts\ObjectiveRepositoryInterface;
use App\DTOs\Objective\ObjectiveDTO;

class ObjectiveCreationService
{
    protected ObjectiveRepositoryInterface $objectiveRepository;

    public function __construct(ObjectiveRepositoryInterface $objectiveRepository)
    {
        $this->objectiveRepository = $objectiveRepository;
    }

    public function createObjective(ObjectiveDTO $objectiveDTO)
    {
        return $this->objectiveRepository->createObjective($objectiveDTO->toArray());
    }
}
