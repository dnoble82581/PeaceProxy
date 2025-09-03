<?php

namespace App\Contracts;

use App\Models\Assessment;
use Illuminate\Database\Eloquent\Collection;

interface RiskAssessmentRepositoryInterface
{
    public function createRiskAssessment(array $data): Assessment;

    public function getRiskAssessment(int $id): Assessment;

    public function getRiskAssessments(): Collection;

    public function getRiskAssessmentsByTenant(int $tenantId): Collection;

    public function getRiskAssessmentsBySubject(int $subjectId): Collection;

    public function updateRiskAssessment(int $id, array $data): Assessment;

    public function deleteRiskAssessment(int $id): bool;
}
