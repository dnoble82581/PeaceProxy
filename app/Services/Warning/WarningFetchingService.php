<?php

namespace App\Services\Warning;

use App\Contracts\WarningRepositoryInterface;
use App\Models\Subject;

class WarningFetchingService
{
    protected WarningRepositoryInterface $warningRepository;

    public function __construct(WarningRepositoryInterface $warningRepository)
    {
        $this->warningRepository = $warningRepository;
    }

    public function fetchWarningById(?int $id = null)
    {
        if (! $id) {
            return null;
        }

        return $this->warningRepository->getWarning($id);
    }

    public function fetchAllWarnings()
    {
        return $this->warningRepository->getWarnings();
    }

    public function fetchWarningsBySubject(Subject $subject)
    {
        return $this->warningRepository->getWarningsBySubject($subject->id);
    }

    public function fetchWarningsBySubjectId(int $subjectId)
    {
        return $this->warningRepository->getWarningsBySubject($subjectId);
    }
}
