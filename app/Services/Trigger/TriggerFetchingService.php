<?php

namespace App\Services\Trigger;

use App\Contracts\TriggerRepositoryInterface;
use App\Models\Trigger;
use Illuminate\Database\Eloquent\Collection;

class TriggerFetchingService
{
    public function __construct(protected TriggerRepositoryInterface $triggerRepository)
    {
    }

    public function getTrigger($id): ?Trigger
    {
        return $this->triggerRepository->getTrigger($id);
    }

    public function getTriggers(): Collection
    {
        return $this->triggerRepository->getTriggers();
    }
}
