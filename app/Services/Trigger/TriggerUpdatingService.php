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

        $log = $this->addLogEntry($trigger);
        logger($log);

        event(new TriggerUpdatedEvent($trigger));
        return $trigger;
    }

    private function addLogEntry(Trigger $trigger)
    {
        $user = auth()->user();

        return app(\App\Services\Log\LogService::class)->write(
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
