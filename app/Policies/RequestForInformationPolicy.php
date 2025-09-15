<?php

namespace App\Policies;

use App\Models\RequestForInformation;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RequestForInformationPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {

    }

    public function view(User $user, RequestForInformation $requestForInformation): bool
    {
    }

    public function create(User $user): bool
    {
    }

    public function update(User $user, RequestForInformation $requestForInformation): bool
    {
    }

    public function delete(User $user, RequestForInformation $requestForInformation): bool
    {
    }

    public function restore(User $user, RequestForInformation $requestForInformation): bool
    {
    }

    public function forceDelete(User $user, RequestForInformation $requestForInformation): bool
    {
    }
}
