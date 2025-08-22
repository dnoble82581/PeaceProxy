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

        $deleted = $this->triggerRepository->deleteTrigger($id);

        event(new TriggerDestroyedEvent($deletedTrigger));

        return $deleted;
    }
}
