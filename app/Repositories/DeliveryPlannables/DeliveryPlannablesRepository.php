<?php

namespace App\Repositories\DeliveryPlannables;

use App\Contracts\DeliveryPlannablesRepositoryInterface;
use App\Models\DeliveryPlannables;
use Illuminate\Database\Eloquent\Collection;

class DeliveryPlannablesRepository implements DeliveryPlannablesRepositoryInterface
{
    public function createDeliveryPlannable($data)
    {
        return DeliveryPlannables::create($data);
    }

    public function getDeliveryPlannables(): Collection
    {
        return DeliveryPlannables::all();
    }

    public function getDeliveryPlannablesByDeliveryPlanId($deliveryPlanId): Collection
    {
        return DeliveryPlannables::where('delivery_plan_id', $deliveryPlanId)->get();
    }

    public function updateDeliveryPlannable($id, $data)
    {
        $deliveryPlannable = $this->getDeliveryPlannable($id);
        $deliveryPlannable->update($data);
        return $deliveryPlannable;
    }

    public function getDeliveryPlannable($id)
    {
        return DeliveryPlannables::find($id);
    }

    public function deleteDeliveryPlannable($id)
    {
        $deliveryPlannable = $this->getDeliveryPlannable($id);
        $deliveryPlannable->delete();
        return $deliveryPlannable;
    }
}
