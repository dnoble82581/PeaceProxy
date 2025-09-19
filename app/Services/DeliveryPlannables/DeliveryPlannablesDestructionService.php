<?php

namespace App\Services\DeliveryPlannables;

use App\Contracts\DeliveryPlannablesRepositoryInterface;
use App\Models\DeliveryPlannables;

class DeliveryPlannablesDestructionService
{
    public function __construct(protected DeliveryPlannablesRepositoryInterface $deliveryPlannablesRepository)
    {
    }

    public function deleteDeliveryPlannable($id)
    {
        $deliveryPlannable = $this->deliveryPlannablesRepository->getDeliveryPlannable($id);

        if (!$deliveryPlannable) {
            return null;
        }

        $this->addLogEntry($deliveryPlannable);

        // Event could be added here if needed
        // event(new DeliveryPlannableDeletedEvent($deliveryPlannable));

        return $this->deliveryPlannablesRepository->deleteDeliveryPlannable($id);
    }

    private function addLogEntry(DeliveryPlannables $deliveryPlannable): void
    {
        $user = auth()->user();

        app(\App\Services\Log\LogService::class)->writeAsync(
            tenantId: tenant()->id,
            event: 'delivery_plannable.deleted',
            headline: "{$user->name} removed an item from a delivery plan",
            about: $deliveryPlannable,      // loggable target
            by: $user,                       // actor
            description: "Removed {$deliveryPlannable->planable_type} from delivery plan",
            properties: [
                'delivery_plan_id' => $deliveryPlannable->delivery_plan_id,
                'planable_type' => $deliveryPlannable->planable_type,
                'planable_id' => $deliveryPlannable->planable_id,
                'role' => $deliveryPlannable->role,
            ],
        );
    }
}
