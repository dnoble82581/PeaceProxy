<?php

namespace App\Services\Assessment;

use App\Contracts\AssessmentRepositoryInterface;
use App\DTOs\Assessment\AssessmentDTO;

class AssessmentCreationService
{
    private function addLogEntry(Assessment $assessment): void
    {
        $user = auth()->user();

        app(\App\Services\Log\LogService::class)->writeAsync(
            tenantId: tenant()->id,
            event: 'assessment.created',
            headline: "{$user->name} created an assessment",
            about: $assessment,      // loggable target
            by: $user,               // actor
            description: "Assessment created for subject ID: {$assessment->subject_id}",
            properties: [
                'subject_id' => $assessment->subject_id,
                'negotiation_id' => $assessment->negotiation_id,
                'assessment_template_id' => $assessment->assessment_template_id,
                'status' => $assessment->status,
            ],
        );
    }
    protected AssessmentRepositoryInterface $assessmentRepository;

    public function __construct(AssessmentRepositoryInterface $assessmentRepository)
    {
        $this->assessmentRepository = $assessmentRepository;
    }

    public function createAssessment(AssessmentDTO $assessmentDTO)
    {
        $assessment = $this->assessmentRepository->createAssessment($assessmentDTO->toArray());

        $this->addLogEntry($assessment);

        return $assessment;
    }
}
