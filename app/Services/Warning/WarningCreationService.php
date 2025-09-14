<?php

namespace App\Services\Warning;

use App\Contracts\WarningRepositoryInterface;
use App\DTOs\Warning\WarningDTO;
use App\Models\Warning;

class WarningCreationService
{
    protected WarningRepositoryInterface $warningRepository;

    public function __construct(WarningRepositoryInterface $warningRepository)
    {
        $this->warningRepository = $warningRepository;
    }

    public function createWarning(WarningDTO $warningDTO)
    {
        $warning = $this->warningRepository->createWarning($warningDTO->toArray());
        $this->addLogEntry($warning);

        return $warning;
    }

    private function addLogEntry(Warning $warning): void
    {
        $user = auth()->user();

        app(\App\Services\Log\LogService::class)->writeAsync(
            tenantId: tenant()->id,
            event: 'warning.created',
            headline: "{$user->name} created a warning",
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
