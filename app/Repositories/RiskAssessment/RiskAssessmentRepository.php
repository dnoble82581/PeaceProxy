<?php

namespace App\Repositories\RiskAssessment;

use App\Contracts\RiskAssessmentRepositoryInterface;
use App\Models\RiskAssessment;
use Illuminate\Database\Eloquent\Collection;

class RiskAssessmentRepository implements RiskAssessmentRepositoryInterface
{
    public function createRiskAssessment(array $data): RiskAssessment
    {
        return RiskAssessment::create($data);
    }

    public function getRiskAssessment(int $id): RiskAssessment
    {
        return RiskAssessment::findOrFail($id);
    }

    public function getRiskAssessments(): Collection
    {
        return RiskAssessment::all();
    }

    public function getRiskAssessmentsByTenant(int $tenantId): Collection
    {
        return RiskAssessment::where('tenant_id', $tenantId)->get();
    }

    public function getRiskAssessmentsBySubject(int $subjectId): Collection
    {
        return RiskAssessment::where('subject_id', $subjectId)->get();
    }

    public function updateRiskAssessment(int $id, array $data): RiskAssessment
    {
        $riskAssessment = $this->getRiskAssessment($id);
        $riskAssessment->update($data);
        return $riskAssessment;
    }

    public function deleteRiskAssessment(int $id): bool
    {
        return RiskAssessment::destroy($id) > 0;
    }
}
