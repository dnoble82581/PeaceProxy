<?php

namespace App\Services\Assessment;

use App\Contracts\AssessmentRepositoryInterface;
use App\DTOs\Assessment\AssessmentDTO;

class AssessmentCreationService
{
    protected AssessmentRepositoryInterface $assessmentRepository;

    public function __construct(AssessmentRepositoryInterface $assessmentRepository)
    {
        $this->assessmentRepository = $assessmentRepository;
    }

    public function createAssessment(AssessmentDTO $assessmentDTO)
    {
        return $this->assessmentRepository->createAssessment($assessmentDTO->toArray());
    }
}
