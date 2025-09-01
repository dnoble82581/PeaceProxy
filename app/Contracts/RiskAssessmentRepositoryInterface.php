<?php

namespace App\Contracts;

use App\Models\RiskAssessment;
use Illuminate\Database\Eloquent\Collection;

interface RiskAssessmentRepositoryInterface
{
    public function createRiskAssessment(array $data): RiskAssessment;

    public function getRiskAssessment(int $id): RiskAssessment;

    public function getRiskAssessments(): Collection;

    public function getRiskAssessmentsByTenant(int $tenantId): Collection;

    public function getRiskAssessmentsBySubject(int $subjectId): Collection;

    public function updateRiskAssessment(int $id, array $data): RiskAssessment;

    public function deleteRiskAssessment(int $id): bool;
}
