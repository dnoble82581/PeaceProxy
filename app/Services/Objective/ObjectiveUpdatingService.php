<?php

namespace App\Services\Objective;

use App\Contracts\ObjectiveRepositoryInterface;
use App\DTOs\Objective\ObjectiveDTO;
use App\Events\Objective\ObjectiveUpdatedEvent;
use App\Models\Objective;

class ObjectiveUpdatingService
{
    public function __construct(protected ObjectiveRepositoryInterface $objectiveRepository)
    {
    }

    public function updateObjective(ObjectiveDTO $objectiveDataDTO, $objectiveId)
    {
        $objective = $this->objectiveRepository->updateObjective($objectiveId, $objectiveDataDTO->toArray());

        if (!$objective) {
            return null;
        }

        $log = $this->addLogEntry($objective);
        logger($log);

        // Dispatch event
        event(new ObjectiveUpdatedEvent($objective));

        return $objective;
    }

    private function addLogEntry(Objective $objective)
    {
        $user = auth()->user();

        return app(\App\Services\Log\LogService::class)->write(
            tenantId: tenant()->id,
            event: 'objective.updated',
            headline: "{$user->name} updated an objective",
            about: $objective,      // loggable target
            by: $user,            // actor
            description: str($objective->title)->limit(140),
            properties: [
                'negotiation_id' => $objective->negotiation_id,
                'status' => $objective->status?->value,
                'priority' => $objective->priority?->value,
            ],
        );
    }
}
