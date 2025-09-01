<?php

namespace App\Policies;

use App\Models\RiskAssessmentQuestion;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RiskAssessmentQuestionsPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {

    }

    public function view(User $user, RiskAssessmentQuestion $riskAssessmentQuestions): bool
    {
    }

    public function create(User $user): bool
    {
    }

    public function update(User $user, RiskAssessmentQuestion $riskAssessmentQuestions): bool
    {
    }

    public function delete(User $user, RiskAssessmentQuestion $riskAssessmentQuestions): bool
    {
    }

    public function restore(User $user, RiskAssessmentQuestion $riskAssessmentQuestions): bool
    {
    }

    public function forceDelete(User $user, RiskAssessmentQuestion $riskAssessmentQuestions): bool
    {
    }
}
