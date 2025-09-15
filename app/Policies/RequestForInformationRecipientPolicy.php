<?php

namespace App\Policies;

use App\Models\RequestForInformationRecipient;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RequestForInformationRecipientPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {

    }

    public function view(User $user, RequestForInformationRecipient $requestForInformationRecipient): bool
    {
    }

    public function create(User $user): bool
    {
    }

    public function update(User $user, RequestForInformationRecipient $requestForInformationRecipient): bool
    {
    }

    public function delete(User $user, RequestForInformationRecipient $requestForInformationRecipient): bool
    {
    }

    public function restore(User $user, RequestForInformationRecipient $requestForInformationRecipient): bool
    {
    }

    public function forceDelete(User $user, RequestForInformationRecipient $requestForInformationRecipient): bool
    {
    }
}
