<?php

namespace App\Services\Trigger;

use App\Contracts\TriggerRepositoryInterface;
use App\DTOs\Trigger\TriggerDTO;
use App\Events\Trigger\TriggerUpdatedEvent;
use App\Models\Trigger;

class TriggerUpdatingService
{
    public function __construct(protected TriggerRepositoryInterface $triggerRepository)
    {
    }

    public function updateTrigger($id, TriggerDTO $triggerDTO): ?Trigger
    {
        $trigger = $this->triggerRepository->updateTrigger($id, $triggerDTO->toArray());

        $this->addLogEntry($trigger);

        event(new TriggerUpdatedEvent($trigger));
        return $trigger;
    }

    private function addLogEntry(Trigger $trigger): void
    {
        $user = auth()->user();

        app(\App\Services\Log\LogService::class)->writeAsync(
            tenantId: tenant()->id,
            event: 'trigger.updated',
            headline: "{$user->name} updated a trigger",
            about: $trigger,      // loggable target
            by: $user,            // actor
            description: str($trigger->description)->limit(140),
            properties: [
                'negotiation_id' => $trigger->negotiation_id,
                'subject_id' => $trigger->subject_id,
            ],
        );
    }
}
