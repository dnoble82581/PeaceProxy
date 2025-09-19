<?php

namespace App\Services\DeliveryPlannables;

use App\Contracts\DeliveryPlannablesRepositoryInterface;
use App\DTOs\DeliveryPlannables\DeliveryPlannablesDTO;
use App\Models\DeliveryPlannables;

class DeliveryPlannablesUpdateService
{
    public function __construct(protected DeliveryPlannablesRepositoryInterface $deliveryPlannablesRepository)
    {
    }

    public function updateDeliveryPlannable(DeliveryPlannablesDTO $deliveryPlannablesDTO, $id)
    {
        $deliveryPlannable = $this->deliveryPlannablesRepository->updateDeliveryPlannable($id, $deliveryPlannablesDTO->toArray());

        $this->addLogEntry($deliveryPlannable);

        // Event could be added here if needed
        // event(new DeliveryPlannableUpdatedEvent($deliveryPlannable));

        return $deliveryPlannable;
    }

    private function addLogEntry(DeliveryPlannables $deliveryPlannable): void
    {
        $user = auth()->user();

        app(\App\Services\Log\LogService::class)->writeAsync(
            tenantId: tenant()->id,
            event: 'delivery_plannable.updated',
            headline: "{$user->name} updated a delivery plan item",
            about: $deliveryPlannable,      // loggable target
            by: $user,                       // actor
            description: "Updated {$deliveryPlannable->planable_type} in delivery plan",
            properties: [
                'delivery_plan_id' => $deliveryPlannable->delivery_plan_id,
                'planable_type' => $deliveryPlannable->planable_type,
                'planable_id' => $deliveryPlannable->planable_id,
                'role' => $deliveryPlannable->role,
            ],
        );
    }
}
