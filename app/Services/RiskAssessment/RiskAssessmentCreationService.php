<?php

namespace App\Services\RiskAssessment;

use App\Contracts\RiskAssessmentRepositoryInterface;
use App\DTOs\RiskAssessment\RiskAssessmentDTO;

class RiskAssessmentCreationService
{
    protected RiskAssessmentRepositoryInterface $riskAssessmentRepository;

    public function __construct(RiskAssessmentRepositoryInterface $riskAssessmentRepository)
    {
        $this->riskAssessmentRepository = $riskAssessmentRepository;
    }

    public function createRiskAssessment(RiskAssessmentDTO $riskAssessmentDTO)
    {
        return $this->riskAssessmentRepository->createRiskAssessment($riskAssessmentDTO->toArray());
    }
}
