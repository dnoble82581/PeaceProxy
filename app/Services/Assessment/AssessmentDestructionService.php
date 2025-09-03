<?php

namespace App\Services\Assessment;

use App\Contracts\AssessmentRepositoryInterface;

class AssessmentDestructionService
{
    protected AssessmentRepositoryInterface $assessmentRepository;

    public function __construct(AssessmentRepositoryInterface $assessmentRepository)
    {
        $this->assessmentRepository = $assessmentRepository;
    }

    public function deleteAssessment($id)
    {
        return $this->assessmentRepository->deleteAssessment($id);
    }
}
