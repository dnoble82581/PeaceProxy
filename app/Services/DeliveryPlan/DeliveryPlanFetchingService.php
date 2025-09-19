<?php

namespace App\Services\DeliveryPlan;

use App\Contracts\DeliveryPlanRepositoryInterface;
use App\DTOs\DeliveryPlan\DeliveryPlanDTO;
use App\Models\DeliveryPlan;
use Illuminate\Database\Eloquent\Collection;

class DeliveryPlanFetchingService
{
    public function __construct(protected DeliveryPlanRepositoryInterface $deliveryPlanRepository)
    {
    }

    public function getDeliveryPlans(): Collection
    {
        return $this->deliveryPlanRepository->getDeliveryPlans();
    }

    public function getDeliveryPlan($id): ?DeliveryPlan
    {
        return $this->deliveryPlanRepository->getDeliveryPlan($id);
    }

    public function getDeliveryPlanDTO($deliveryPlanId): ?DeliveryPlanDTO
    {
        $deliveryPlan = $this->getDeliveryPlan($deliveryPlanId);

        if (!$deliveryPlan) {
            return null;
        }

        return DeliveryPlanDTO::fromArray($deliveryPlan->toArray());
    }
}
