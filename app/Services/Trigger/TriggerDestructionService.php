<?php

namespace App\Services\Trigger;

use App\Contracts\TriggerRepositoryInterface;
use App\Events\Trigger\TriggerDestroyedEvent;
use App\Models\Trigger;

class TriggerDestructionService
{
    public function __construct(protected TriggerRepositoryInterface $triggerRepository)
    {
    }

    public function deleteTrigger($id): ?Trigger
    {
        $deletedTrigger = $this->triggerRepository->getTrigger($id);

        if (!$deletedTrigger) {
            return null;
        }

        $log = $this->addLogEntry($deletedTrigger);
        logger($log);

        $deleted = $this->triggerRepository->deleteTrigger($id);

        event(new TriggerDestroyedEvent($deletedTrigger));

        return $deleted;
    }

    private function addLogEntry(Trigger $trigger)
    {
        $user = auth()->user();

        return app(\App\Services\Log\LogService::class)->write(
            tenantId: tenant()->id,
            event: 'trigger.deleted',
            headline: "{$user->name} deleted a trigger",
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
