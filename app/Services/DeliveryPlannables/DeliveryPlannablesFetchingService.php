<?php

namespace App\Services\DeliveryPlannables;

use App\Contracts\DeliveryPlannablesRepositoryInterface;
use App\DTOs\DeliveryPlannables\DeliveryPlannablesDTO;
use App\Models\DeliveryPlannables;
use Illuminate\Database\Eloquent\Collection;

class DeliveryPlannablesFetchingService
{
    public function __construct(protected DeliveryPlannablesRepositoryInterface $deliveryPlannablesRepository)
    {
    }

    public function getDeliveryPlannables(): Collection
    {
        return $this->deliveryPlannablesRepository->getDeliveryPlannables();
    }

    public function getDeliveryPlannablesByDeliveryPlanId($deliveryPlanId): Collection
    {
        return $this->deliveryPlannablesRepository->getDeliveryPlannablesByDeliveryPlanId($deliveryPlanId);
    }

    public function getDeliveryPlannable($id): ?DeliveryPlannables
    {
        return $this->deliveryPlannablesRepository->getDeliveryPlannable($id);
    }

    public function getDeliveryPlannableDTO($deliveryPlannableId): ?DeliveryPlannablesDTO
    {
        $deliveryPlannable = $this->getDeliveryPlannable($deliveryPlannableId);

        if (!$deliveryPlannable) {
            return null;
        }

        return DeliveryPlannablesDTO::fromArray($deliveryPlannable->toArray());
    }
}
