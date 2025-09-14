<?php

namespace App\Services\Warning;

use App\Contracts\WarningRepositoryInterface;
use App\Models\Warning;

class WarningDestructionService
{
    public function __construct(protected WarningRepositoryInterface $warningRepository)
    {
    }

    public function deleteWarning($warningId)
    {
        // Get the warning before deleting it
        $warning = $this->warningRepository->getWarning($warningId);

        if (!$warning) {
            return null;
        }

        $this->addLogEntry($warning);

        return $this->warningRepository->deleteWarning($warningId);
    }

    private function addLogEntry(Warning $warning): void
    {
        $user = auth()->user();

        app(\App\Services\Log\LogService::class)->writeAsync(
            tenantId: tenant()->id,
            event: 'warning.deleted',
            headline: "{$user->name} deleted a warning",
            about: $warning,      // loggable target
            by: $user,            // actor
            description: str($warning->description)->limit(140),
            properties: [
                'subject_id' => $warning->subject_id,
                'warning_type' => $warning->warning_type?->value,
                'risk_level' => $warning->risk_level?->value,
            ],
        );
    }
}
