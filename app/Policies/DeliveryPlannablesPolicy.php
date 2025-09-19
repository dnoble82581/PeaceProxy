<?php

namespace App\Policies;

use App\Models\DeliveryPlannables;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DeliveryPlannablesPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {

    }

    public function view(User $user, DeliveryPlannables $deliveryPlannables): bool
    {
    }

    public function create(User $user): bool
    {
    }

    public function update(User $user, DeliveryPlannables $deliveryPlannables): bool
    {
    }

    public function delete(User $user, DeliveryPlannables $deliveryPlannables): bool
    {
    }

    public function restore(User $user, DeliveryPlannables $deliveryPlannables): bool
    {
    }

    public function forceDelete(User $user, DeliveryPlannables $deliveryPlannables): bool
    {
    }
}
