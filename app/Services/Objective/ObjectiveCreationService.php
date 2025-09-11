<?php

namespace App\Services\Objective;

use App\Contracts\ObjectiveRepositoryInterface;
use App\DTOs\Objective\ObjectiveDTO;
use App\Models\Objective;

class ObjectiveCreationService
{
    protected ObjectiveRepositoryInterface $objectiveRepository;

    public function __construct(ObjectiveRepositoryInterface $objectiveRepository)
    {
        $this->objectiveRepository = $objectiveRepository;
    }

    public function createObjective(ObjectiveDTO $objectiveDTO)
    {
        $objective = $this->objectiveRepository->createObjective($objectiveDTO->toArray());

        $log = $this->addLogEntry($objective);
        logger($log);

        return $objective;
    }

    private function addLogEntry(Objective $objective)
    {
        $user = auth()->user();

        return app(\App\Services\Log\LogService::class)->write(
            tenantId: tenant()->id,
            event: 'objective.created',
            headline: "{$user->name} created an objective",
            about: $objective,      // loggable target
            by: $user,            // actor
            description: str($objective->objective)->limit(140),
            properties: [
                'negotiation_id' => $objective->negotiation_id,
                'status' => $objective->status?->value,
                'priority' => $objective->priority?->value,
            ],
        );
    }
}
