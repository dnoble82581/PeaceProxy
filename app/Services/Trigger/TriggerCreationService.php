<?php

namespace App\Services\Trigger;

use App\Contracts\TriggerRepositoryInterface;
use App\DTOs\Trigger\TriggerDTO;
use App\Events\Trigger\TriggerCreatedEvent;
use App\Models\Trigger;

class TriggerCreationService
{
    public function __construct(protected TriggerRepositoryInterface $triggerRepository)
    {
    }

    public function createTrigger(TriggerDTO $triggerDTO)
    {
        $trigger = $this->triggerRepository->createTrigger($triggerDTO->toArray());

        $log = $this->addLogEntry($trigger);
        logger($log);

        event(new TriggerCreatedEvent($trigger));

        return $trigger;
    }

    private function addLogEntry(Trigger $trigger)
    {
        $user = auth()->user();

        return app(\App\Services\Log\LogService::class)->write(
            tenantId: tenant()->id,
            event: 'trigger.created',
            headline: "{$user->name} created a trigger",
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
