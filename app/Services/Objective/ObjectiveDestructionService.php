<?php

namespace App\Services\Objective;

use App\Contracts\ObjectiveRepositoryInterface;

class ObjectiveDestructionService
{
    public function __construct(protected ObjectiveRepositoryInterface $objectiveRepository)
    {
    }

    public function deleteObjective($objectiveId)
    {
        $this->objectiveRepository->deleteObjective($objectiveId);
    }
}
