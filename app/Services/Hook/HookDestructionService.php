<?php

namespace App\Services\Hook;

use App\Contracts\HookRepositoryInterface;
use App\Events\Hook\HookDestroyedEvent;

class HookDestructionService
{
    public function __construct(protected HookRepositoryInterface $hookRepository)
    {
    }

    public function deleteHook($hookId)
    {
        $hook = $this->hookRepository->getHook($hookId);

        if (!$hook) {
            return null;
        }

        // Dispatch event if needed
        event(new HookDestroyedEvent($hook));

        return $this->hookRepository->deleteHook($hookId);
    }
}
