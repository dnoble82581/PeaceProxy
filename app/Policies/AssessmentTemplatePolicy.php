<?php

namespace App\Policies;

use App\Models\AssessmentTemplate;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AssessmentTemplatePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {

    }

    public function view(User $user, AssessmentTemplate $assessmentTemplate): bool
    {
    }

    public function create(User $user): bool
    {
    }

    public function update(User $user, AssessmentTemplate $assessmentTemplate): bool
    {
    }

    public function delete(User $user, AssessmentTemplate $assessmentTemplate): bool
    {
    }

    public function restore(User $user, AssessmentTemplate $assessmentTemplate): bool
    {
    }

    public function forceDelete(User $user, AssessmentTemplate $assessmentTemplate): bool
    {
    }
}
