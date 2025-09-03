<?php

namespace App\Policies;

use App\Models\AssessmentQuestionsAnswer;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AssessmentQuestionsAnswerPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {

    }

    public function view(User $user, AssessmentQuestionsAnswer $assessmentQuestionsAnswer): bool
    {
    }

    public function create(User $user): bool
    {
    }

    public function update(User $user, AssessmentQuestionsAnswer $assessmentQuestionsAnswer): bool
    {
    }

    public function delete(User $user, AssessmentQuestionsAnswer $assessmentQuestionsAnswer): bool
    {
    }

    public function restore(User $user, AssessmentQuestionsAnswer $assessmentQuestionsAnswer): bool
    {
    }

    public function forceDelete(User $user, AssessmentQuestionsAnswer $assessmentQuestionsAnswer): bool
    {
    }
}
