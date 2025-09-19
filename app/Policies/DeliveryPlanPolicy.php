<?php

namespace App\Policies;

use App\Models\DeliveryPlan;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DeliveryPlanPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any delivery plans.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return true; // All authenticated users can view delivery plans
    }

    /**
     * Determine whether the user can view the delivery plan.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\DeliveryPlan  $deliveryPlan
     * @return bool
     */
    public function view(User $user, DeliveryPlan $deliveryPlan): bool
    {
        return true; // All authenticated users can view delivery plans
    }

    /**
     * Determine whether the user can create delivery plans.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return true; // All authenticated users can create delivery plans
    }

    /**
     * Determine whether the user can update the delivery plan.
     * Only the creator can update the delivery plan.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\DeliveryPlan  $deliveryPlan
     * @return bool
     */
    public function update(User $user, DeliveryPlan $deliveryPlan): bool
    {
        return $user->id === $deliveryPlan->created_by;
    }

    /**
     * Determine whether the user can delete the delivery plan.
     * Only the creator can delete the delivery plan.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\DeliveryPlan  $deliveryPlan
     * @return bool
     */
    public function delete(User $user, DeliveryPlan $deliveryPlan): bool
    {
        return $user->id === $deliveryPlan->created_by;
    }

    /**
     * Determine whether the user can restore the delivery plan.
     * Only the creator can restore the delivery plan.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\DeliveryPlan  $deliveryPlan
     * @return bool
     */
    public function restore(User $user, DeliveryPlan $deliveryPlan): bool
    {
        return $user->id === $deliveryPlan->created_by;
    }

    /**
     * Determine whether the user can permanently delete the delivery plan.
     * Only the creator can permanently delete the delivery plan.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\DeliveryPlan  $deliveryPlan
     * @return bool
     */
    public function forceDelete(User $user, DeliveryPlan $deliveryPlan): bool
    {
        return $user->id === $deliveryPlan->created_by;
    }
}
