<?php

namespace App\Services\Hook;

use App\Contracts\HookRepositoryInterface;
use App\DTOs\Hook\HookDTO;
use App\Events\Hook\HookCreatedEvent;
use App\Models\Hook;

class HookCreationService
{
    public function __construct(protected HookRepositoryInterface $hookRepository)
    {
    }

    public function createHook(HookDTO $hookDTO)
    {
        $hook = $this->hookRepository->createHook($hookDTO->toArray());
        $this->addLogEntry($hook);
        // Dispatch event on private.negotiation channel
        event(new HookCreatedEvent($hook->negotiation_id, $hook->id));

        return $hook;
    }

    private function addLogEntry(Hook $hook): void
    {
        $user = auth()->user();
        app(\App\Services\Log\LogService::class)->writeAsync(
            tenantId: tenant()->id,
            event: 'hook.created',
            headline: "{$user->name} created a hook",
            about: $hook,      // loggable target
            by: $user,            // actor
            description: str($hook->description)->limit(140),
            properties: [
                'subject_id' => $hook->subject_id,
                'category' => $hook->category?->value,
                'sensitivity_level' => $hook->sensitivity_level?->value,
            ],
        );
    }
}
