<?php

namespace App\Services\Objective;

use App\Contracts\ObjectiveRepositoryInterface;
use App\DTOs\Objective\ObjectiveDTO;

class ObjectiveUpdatingService
{
    public function __construct(protected ObjectiveRepositoryInterface $objectiveRepository)
    {
    }

    public function updateObjective(ObjectiveDTO $objectiveDataDTO, $objectiveId)
    {
        return $this->objectiveRepository->updateObjective($objectiveId, $objectiveDataDTO->toArray());
    }
}
