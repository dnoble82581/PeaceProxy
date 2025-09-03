<?php

namespace App\Services\RiskAssessment;

use App\Contracts\RiskAssessmentRepositoryInterface;
use App\DTOs\RiskAssessment\RiskAssessmentDTO;
use App\Models\Assessment;

class RiskAssessmentUpdatingService
{
    protected RiskAssessmentRepositoryInterface $riskAssessmentRepository;

    public function __construct(RiskAssessmentRepositoryInterface $riskAssessmentRepository)
    {
        $this->riskAssessmentRepository = $riskAssessmentRepository;
    }

    public function updateRiskAssessment(int $id, RiskAssessmentDTO $riskAssessmentDTO): Assessment
    {
        return $this->riskAssessmentRepository->updateRiskAssessment($id, $riskAssessmentDTO->toArray());
    }
}
