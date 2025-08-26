<?php

namespace App\Services\Hook;

use App\Contracts\HookRepositoryInterface;
use App\DTOs\Hook\HookDTO;
use App\Events\Hook\HookCreatedEvent;

class HookCreationService
{
    public function __construct(protected HookRepositoryInterface $hookRepository)
    {
    }

    public function createHook(HookDTO $hookDTO)
    {
        $hook = $this->hookRepository->createHook($hookDTO->toArray());

        // Dispatch event on private.negotiation channel
        event(new HookCreatedEvent($hook));

        return $hook;
    }
}
