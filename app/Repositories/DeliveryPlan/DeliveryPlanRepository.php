<?php

namespace App\Repositories\DeliveryPlan;

use App\Contracts\DeliveryPlanRepositoryInterface;
use App\Models\DeliveryPlan;
use Illuminate\Database\Eloquent\Collection;

class DeliveryPlanRepository implements DeliveryPlanRepositoryInterface
{
    public function createDeliveryPlan($data)
    {
        return DeliveryPlan::create($data);
    }

    public function getDeliveryPlans(): Collection
    {
        return DeliveryPlan::all();
    }

    public function updateDeliveryPlan($id, $data)
    {
        $deliveryPlan = $this->getDeliveryPlan($id);
        $deliveryPlan->update($data);
        return $deliveryPlan;
    }

    public function getDeliveryPlan($id)
    {
        return DeliveryPlan::find($id);
    }

    public function deleteDeliveryPlan($id)
    {
        $deliveryPlan = $this->getDeliveryPlan($id);
        $deliveryPlan->delete();
        return $deliveryPlan;
    }
}
