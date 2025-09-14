<?php

namespace App\Services\Warning;

use App\Contracts\WarningRepositoryInterface;
use App\DTOs\Warning\WarningDTO;
use App\Models\Warning;

class WarningUpdatingService
{
    public function __construct(protected WarningRepositoryInterface $warningRepository)
    {
    }

    public function updateWarning(WarningDTO $warningDataDTO, $warningId)
    {
        $warning = $this->warningRepository->updateWarning($warningId, $warningDataDTO->toArray());

        if (!$warning) {
            return null;
        }

        $this->addLogEntry($warning);

        return $warning;
    }

    private function addLogEntry(Warning $warning): void
    {
        $user = auth()->user();

        app(\App\Services\Log\LogService::class)->writeAsync(
            tenantId: tenant()->id,
            event: 'warning.updated',
            headline: "{$user->name} updated a warning",
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
