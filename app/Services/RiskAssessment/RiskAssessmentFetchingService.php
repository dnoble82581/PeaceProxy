<?php

namespace App\Services\RiskAssessment;

use App\Contracts\RiskAssessmentRepositoryInterface;
use App\Models\RiskAssessment;
use Illuminate\Database\Eloquent\Collection;

class RiskAssessmentFetchingService
{
    protected RiskAssessmentRepositoryInterface $riskAssessmentRepository;

    public function __construct(RiskAssessmentRepositoryInterface $riskAssessmentRepository)
    {
        $this->riskAssessmentRepository = $riskAssessmentRepository;
    }

    public function getRiskAssessment(int $id): RiskAssessment
    {
        return $this->riskAssessmentRepository->getRiskAssessment($id);
    }

    public function getRiskAssessments(): Collection
    {
        return $this->riskAssessmentRepository->getRiskAssessments();
    }

    public function getRiskAssessmentsByTenant(int $tenantId): Collection
    {
        return $this->riskAssessmentRepository->getRiskAssessmentsByTenant($tenantId);
    }

    public function getRiskAssessmentsBySubject(int $subjectId): Collection
    {
        return $this->riskAssessmentRepository->getRiskAssessmentsBySubject($subjectId);
    }
}
