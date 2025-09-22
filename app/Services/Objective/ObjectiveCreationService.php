<?php

namespace App\Services\Objective;

use App\Contracts\ObjectiveRepositoryInterface;
use App\DTOs\Objective\ObjectiveDTO;
use App\Events\Objective\ObjectiveCreatedEvent;
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

        event(new ObjectiveCreatedEvent($objective->negotiation_id, $objective->id));
        $this->addLogEntry($objective);

        return $objective;
    }

    private function addLogEntry(Objective $objective): void
    {
        $user = auth()->user();

        app(\App\Services\Log\LogService::class)->writeAsync(
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
