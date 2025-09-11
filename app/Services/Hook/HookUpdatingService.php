<?php

namespace App\Services\Hook;

use App\Contracts\HookRepositoryInterface;
use App\DTOs\Hook\HookDTO;
use App\Events\Hook\HookUpdatedEvent;
use App\Models\Hook;

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

        $log = $this->addLogEntry($hook);
        logger($log);

        // Dispatch event if needed
        event(new HookUpdatedEvent($hook));

        return $hook;
    }

    private function addLogEntry(Hook $hook)
    {
        $user = auth()->user();

        return app(\App\Services\Log\LogService::class)->write(
            tenantId: tenant()->id,
            event: 'hook.updated',
            headline: "{$user->name} updated a hook",
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
