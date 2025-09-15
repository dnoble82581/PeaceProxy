<?php

namespace App\Policies;

use App\Models\RequestForInformationReply;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RequestForInformationReplyPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {

    }

    public function view(User $user, RequestForInformationReply $requestForInformationReply): bool
    {
    }

    public function create(User $user): bool
    {
    }

    public function update(User $user, RequestForInformationReply $requestForInformationReply): bool
    {
    }

    public function delete(User $user, RequestForInformationReply $requestForInformationReply): bool
    {
    }

    public function restore(User $user, RequestForInformationReply $requestForInformationReply): bool
    {
    }

    public function forceDelete(User $user, RequestForInformationReply $requestForInformationReply): bool
    {
    }
}
