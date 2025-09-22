<?php

namespace App\Services\Objective;

use App\Contracts\ObjectiveRepositoryInterface;
use App\Events\Objective\ObjectiveDeletedEvent;
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

        $this->addLogEntry($objective);

        // Dispatch event
        if ($objective) {
            $data = [
                'objectiveId' => $objective->id,
                'negotiationId' => $objective->negotiation_id,
                'actorId' => auth()->user()->id,
                'actorName' => auth()->user()->name,
                'priority' => $objective->priority,
                'objectiveLabel' => $objective->objective,
            ];
        }


        $is_deleted = $this->objectiveRepository->deleteObjective($objectiveId);

        if ($is_deleted) {
            event(new ObjectiveDeletedEvent($data));
        }

        return $is_deleted;
    }

    private function addLogEntry(Objective $objective): void
    {
        $user = auth()->user();

        app(\App\Services\Log\LogService::class)->writeAsync(
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
