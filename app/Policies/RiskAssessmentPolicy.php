<?php

namespace App\Policies;

use App\Models\RiskAssessment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RiskAssessmentPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {

    }

    public function view(User $user, RiskAssessment $riskAssessment): bool
    {
    }

    public function create(User $user): bool
    {
    }

    public function update(User $user, RiskAssessment $riskAssessment): bool
    {
    }

    public function delete(User $user, RiskAssessment $riskAssessment): bool
    {
    }

    public function restore(User $user, RiskAssessment $riskAssessment): bool
    {
    }

    public function forceDelete(User $user, RiskAssessment $riskAssessment): bool
    {
    }
}
