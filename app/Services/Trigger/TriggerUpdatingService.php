<?php

namespace App\Services\Trigger;

use App\Contracts\TriggerRepositoryInterface;
use App\DTOs\Trigger\TriggerDTO;
use App\Models\Trigger;

class TriggerUpdatingService
{
    public function __construct(protected TriggerRepositoryInterface $triggerRepository)
    {
    }

    public function updateTrigger($id, TriggerDTO $triggerDTO): ?Trigger
    {
        return $this->triggerRepository->updateTrigger($id, $triggerDTO->toArray());
    }
}
