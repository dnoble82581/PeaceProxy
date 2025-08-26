<?php

namespace App\Services\Trigger;

use App\Contracts\TriggerRepositoryInterface;
use App\DTOs\Trigger\TriggerDTO;
use App\Events\Trigger\TriggerCreatedEvent;

class TriggerCreationService
{
    public function __construct(protected TriggerRepositoryInterface $triggerRepository)
    {
    }

    public function createTrigger(TriggerDTO $triggerDTO)
    {
        $trigger = $this->triggerRepository->createTrigger($triggerDTO->toArray());

        event(new TriggerCreatedEvent($trigger));

        return $trigger;
    }
}
