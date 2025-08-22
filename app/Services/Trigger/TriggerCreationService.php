<?php

namespace App\Services\Trigger;

use App\Contracts\TriggerRepositoryInterface;
use App\DTOs\Trigger\TriggerDTO;

class TriggerCreationService
{
    public function __construct(protected TriggerRepositoryInterface $triggerRepository)
    {
    }

    public function createTrigger(TriggerDTO $triggerDTO)
    {
        return $this->triggerRepository->createTrigger($triggerDTO->toArray());
    }
}
