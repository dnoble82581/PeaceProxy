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
        $this->addLogEntry($warning);
        return $warning;
    }

    private function addLogEntry(Warning $warning)
    {
        $user = auth()->user();

        return app(\App\Services\Log\LogService::class)->write(
            tenantId: tenant()->id,
            event: 'warning.updated',
            headline: "{$user->name} updated a warning.",
            about: $warning,      // loggable target
            by: $user,            // actor
            description: 'Warning updated for subject',
            properties: [
                'subject_id' => $warning->subject_id,
                'logged_by_id' => $warning->created_by_id,
            ],
        );
    }
}
