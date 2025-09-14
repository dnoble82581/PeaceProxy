<?php

namespace App\Repositories\RiskAssessment;

use App\Contracts\RiskAssessmentRepositoryInterface;
use App\Models\Assessment;
use Illuminate\Database\Eloquent\Collection;

class RiskAssessmentRepository implements RiskAssessmentRepositoryInterface
{
    /**
     * Create a new risk assessment
     *
     * @param array $data
     * @return Assessment
     */
    public function createRiskAssessment(array $data): Assessment
    {
        return Assessment::create($data);
    }

    /**
     * Get a risk assessment by ID
     *
     * @param int $id
     * @return Assessment
     */
    public function getRiskAssessment(int $id): Assessment
    {
        return Assessment::findOrFail($id);
    }

    /**
     * Get all risk assessments
     *
     * @return Collection
     */
    public function getRiskAssessments(): Collection
    {
        return Assessment::all();
    }

    /**
     * Get risk assessments by tenant ID
     *
     * @param int $tenantId
     * @return Collection
     */
    public function getRiskAssessmentsByTenant(int $tenantId): Collection
    {
        return Assessment::where('tenant_id', $tenantId)->get();
    }

    /**
     * Get risk assessments by subject ID
     *
     * @param int $subjectId
     * @return Collection
     */
    public function getRiskAssessmentsBySubject(int $subjectId): Collection
    {
        return Assessment::where('subject_id', $subjectId)->get();
    }

    /**
     * Update a risk assessment
     *
     * @param int $id
     * @param array $data
     * @return Assessment
     */
    public function updateRiskAssessment(int $id, array $data): Assessment
    {
        $assessment = $this->getRiskAssessment($id);
        $assessment->update($data);
        return $assessment;
    }

    /**
     * Delete a risk assessment
     *
     * @param int $id
     * @return bool
     */
    public function deleteRiskAssessment(int $id): bool
    {
        return $this->getRiskAssessment($id)->delete();
    }
}
