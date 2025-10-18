<?php

namespace App\Services\User;

use App\Models\User;

class UpdateUserService
{
    public function updateUser(User $user, array $data): User
    {
        $user->fill($data)->save();

        return $user->refresh();
    }
}
