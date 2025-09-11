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
        $log = $this->addLogEntry($hook);
        logger($log);
        // Dispatch event on private.negotiation channel
        event(new HookCreatedEvent($hook));

        return $hook;
    }

    private function addLogEntry(Hook $hook)
    {
        $user = auth()->user();
        return app(\App\Services\Log\LogService::class)->write(
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
