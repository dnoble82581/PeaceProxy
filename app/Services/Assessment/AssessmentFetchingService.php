<?php

namespace App\Services\Assessment;

use App\Contracts\AssessmentRepositoryInterface;
use App\DTOs\Assessment\AssessmentDTO;

class AssessmentFetchingService
{
    protected AssessmentRepositoryInterface $assessmentRepository;

    public function __construct(AssessmentRepositoryInterface $assessmentRepository)
    {
        $this->assessmentRepository = $assessmentRepository;
    }

    public function getAssessment($id)
    {
        return $this->assessmentRepository->getAssessment($id);
    }

    public function getAssessments()
    {
        return $this->assessmentRepository->getAssessments();
    }

    public function getAssessmentDTO($assessmentId): ?AssessmentDTO
    {
        $assessment = $this->getAssessment($assessmentId);

        if (! $assessment) {
            return null;
        }

        return AssessmentDTO::fromArray($assessment->toArray());
    }
}
