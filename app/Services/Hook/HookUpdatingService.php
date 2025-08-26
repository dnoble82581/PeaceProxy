<?php

namespace App\Services\Hook;

use App\Contracts\HookRepositoryInterface;
use App\DTOs\Hook\HookDTO;
use App\Events\Hook\HookUpdatedEvent;

class HookUpdatingService
{
    public function __construct(protected HookRepositoryInterface $hookRepository)
    {
    }

    public function updateHook($hookId, HookDTO $hookDTO)
    {
        $hook = $this->hookRepository->updateHook($hookId, $hookDTO->toArray());

        if (! $hook) {
            return null;
        }

        // Dispatch event if needed
        event(new HookUpdatedEvent($hook));

        return $hook;
    }
}
