<?php

namespace App\Services\Warning;

use App\Contracts\WarningRepositoryInterface;
use App\Events\Warning\WarningDeletedEvent;

class WarningDeletionService
{
    protected WarningRepositoryInterface $warningRepository;

    public function __construct(WarningRepositoryInterface $warningRepository)
    {

        $this->warningRepository = $warningRepository;
    }

    public function deleteWarning(int $id): void
    {
        $warning = $this->warningRepository->getWarning($id);
        if ($warning) {
            $details = [
                'label' => $warning->warning_type?->label() ?? 'Unknown',
                'createdBy' => $warning->createdBy->name ?? 'Unknown User',
                'risk_level' => $warning->risk_level?->label() ?? 'Unknown',
                'subjectName' => $warning->subject->name ?? 'the subject',
                'warningId' => $warning->id,
            ];

            $subjectId = $warning->subject_id;

            event(new WarningDeletedEvent($warning->subject_id, $details));
            $this->warningRepository->deleteWarning($id);

            $this->addLogEntry($details, $subjectId);
        }

    }

    private function addLogEntry(array $details, $subjectId)
    {
        $user = auth()->user();

        return app(\App\Services\Log\LogService::class)->write(
            tenantId: tenant()->id,
            event: 'warning.deleted',
            headline: "{$user->name} deleted a warning.",
            about: null,      // loggable target
            by: $user,            // actor
            description: 'Warning deleted for subject',
            properties: [
                'subject_id' => $subjectId,
                'logged_by_id' => $details['createdBy'],
                'warning_type' => $details['label'],
                'risk_level' => $details['risk_level'],
                'subject_name' => $details['subjectName'],
                'warning_id' => $details['warningId'],
            ],
        );
    }
}
