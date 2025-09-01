<?php

namespace App\Services\RiskAssessment;

use App\Contracts\RiskAssessmentRepositoryInterface;

class RiskAssessmentDestructionService
{
    protected RiskAssessmentRepositoryInterface $riskAssessmentRepository;

    public function __construct(RiskAssessmentRepositoryInterface $riskAssessmentRepository)
    {
        $this->riskAssessmentRepository = $riskAssessmentRepository;
    }

    public function deleteRiskAssessment(int $id): bool
    {
        return $this->riskAssessmentRepository->deleteRiskAssessment($id);
    }
}
