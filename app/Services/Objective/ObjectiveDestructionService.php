<?php

namespace App\Services\Objective;

use App\Contracts\ObjectiveRepositoryInterface;
use App\Events\Objective\ObjectiveDestroyedEvent;
use App\Models\Objective;

class ObjectiveDestructionService
{
    public function __construct(protected ObjectiveRepositoryInterface $objectiveRepository)
    {
    }

    public function deleteObjective($objectiveId)
    {
        // Get the objective before deleting it
        $objective = $this->objectiveRepository->getObjective($objectiveId);

        if (!$objective) {
            return null;
        }

        $log = $this->addLogEntry($objective);
        logger($log);

        // Dispatch event
        event(new ObjectiveDestroyedEvent($objective));

        return $this->objectiveRepository->deleteObjective($objectiveId);
    }

    private function addLogEntry(Objective $objective)
    {
        $user = auth()->user();

        return app(\App\Services\Log\LogService::class)->write(
            tenantId: tenant()->id,
            event: 'objective.deleted',
            headline: "{$user->name} deleted an objective",
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
