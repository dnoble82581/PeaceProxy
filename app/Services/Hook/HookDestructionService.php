<?php

namespace App\Services\Hook;

use App\Contracts\HookRepositoryInterface;
use App\Events\Hook\HookDestroyedEvent;
use App\Models\Hook;

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

        $log = $this->addLogEntry($hook);
        logger($log);

        // Dispatch event if needed
        event(new HookDestroyedEvent($hook));

        return $this->hookRepository->deleteHook($hookId);
    }

    private function addLogEntry(Hook $hook)
    {
        $user = auth()->user();

        return app(\App\Services\Log\LogService::class)->write(
            tenantId: tenant()->id,
            event: 'hook.deleted',
            headline: "{$user->name} deleted a hook",
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
