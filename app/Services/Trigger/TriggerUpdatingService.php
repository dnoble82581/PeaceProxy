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
        event(new TriggerUpdatedEvent($trigger));
        return $trigger;
    }
}
