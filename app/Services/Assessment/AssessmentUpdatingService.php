<?php

namespace App\Services\Assessment;

use App\Contracts\AssessmentRepositoryInterface;
use App\DTOs\Assessment\AssessmentDTO;

class AssessmentUpdatingService
{
    protected AssessmentRepositoryInterface $assessmentRepository;

    public function __construct(AssessmentRepositoryInterface $assessmentRepository)
    {
        $this->assessmentRepository = $assessmentRepository;
    }

    public function updateAssessment($id, AssessmentDTO $assessmentDTO)
    {
        return $this->assessmentRepository->updateAssessment($id, $assessmentDTO->toArray());
    }
}
