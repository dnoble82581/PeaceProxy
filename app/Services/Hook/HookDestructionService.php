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

        if (! $hook) {
            return null;
        }

        $data = [
            'hookId' => $hook->id,
            'negotiationId' => $hook->negotiation_id,
        ];

        $this->addLogEntry($hook);

        $hookDeleted = $this->hookRepository->deleteHook($hookId);

        if ($hookDeleted) {
            event(new HookDestroyedEvent($data['negotiationId'], $data['hookId']));
        }
        // Dispatch event if needed

        return $hookDeleted;

    }

    private function addLogEntry(Hook $hook): void
    {
        $user = auth()->user();

        app(\App\Services\Log\LogService::class)->writeAsync(
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
