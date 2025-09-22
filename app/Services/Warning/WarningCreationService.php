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

    private function addLogEntry(Warning $warning)
    {
        $user = auth()->user();

        return app(\App\Services\Log\LogService::class)->write(
            tenantId: tenant()->id,
            event: 'warning.created',
            headline: "{$user->name} created a warning.",
            about: $warning,      // loggable target
            by: $user,            // actor
            description: 'Warning created for subject',
            properties: [
                'subject_id' => $warning->subject_id,
                'logged_by_id' => $warning->created_by_id,
            ],
        );
    }
}
