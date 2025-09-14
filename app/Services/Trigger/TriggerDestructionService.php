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

        $this->addLogEntry($deletedTrigger);

        $deleted = $this->triggerRepository->deleteTrigger($id);

        event(new TriggerDestroyedEvent($deletedTrigger));

        return $deleted;
    }

    private function addLogEntry(Trigger $trigger): void
    {
        $user = auth()->user();

        app(\App\Services\Log\LogService::class)->writeAsync(
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
