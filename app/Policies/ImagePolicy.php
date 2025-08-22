<?php

namespace App\Policies;

use App\Models\Image;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ImagePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
    }

    public function view(User $user, Image $image): bool
    {
    }

    public function create(User $user): bool
    {
    }

    public function update(User $user, Image $image): bool
    {
    }

    public function delete(User $user, Image $image): bool
    {
    }

    public function restore(User $user, Image $image): bool
    {
    }

    public function forceDelete(User $user, Image $image): bool
    {
    }
}
