<?php

namespace App\Policies;

use App\Models\AssessmentTemplateQuestion;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AssessmentTemplateQuestionPolicy{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        //
    }

    public function view(User $user, AssessmentTemplateQuestion $assessmentTemplateQuestion): bool
    {
    }

    public function create(User $user): bool
    {
    }

    public function update(User $user, AssessmentTemplateQuestion $assessmentTemplateQuestion): bool
    {
    }

    public function delete(User $user, AssessmentTemplateQuestion $assessmentTemplateQuestion): bool
    {
    }

    public function restore(User $user, AssessmentTemplateQuestion $assessmentTemplateQuestion): bool
    {
    }

    public function forceDelete(User $user, AssessmentTemplateQuestion $assessmentTemplateQuestion): bool
    {
    }
}
