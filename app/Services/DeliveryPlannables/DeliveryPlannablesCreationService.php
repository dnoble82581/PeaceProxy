<?php

namespace App\Services\DeliveryPlannables;

use App\Contracts\DeliveryPlannablesRepositoryInterface;
use App\DTOs\DeliveryPlannables\DeliveryPlannablesDTO;
use App\Models\DeliveryPlannables;

class DeliveryPlannablesCreationService
{
    public function __construct(protected DeliveryPlannablesRepositoryInterface $deliveryPlannablesRepository)
    {
    }

    public function createDeliveryPlannable(DeliveryPlannablesDTO $deliveryPlannablesDTO)
    {
        $deliveryPlannable = $this->deliveryPlannablesRepository->createDeliveryPlannable($deliveryPlannablesDTO->toArray());

        $this->addLogEntry($deliveryPlannable);

        // Event could be added here if needed
        // event(new DeliveryPlannableCreatedEvent($deliveryPlannable));

        return $deliveryPlannable;
    }

    private function addLogEntry(DeliveryPlannables $deliveryPlannable): void
    {
        $user = auth()->user();

        app(\App\Services\Log\LogService::class)->writeAsync(
            tenantId: tenant()->id,
            event: 'delivery_plannable.created',
            headline: "{$user->name} added an item to a delivery plan",
            about: $deliveryPlannable,      // loggable target
            by: $user,                       // actor
            description: "Added {$deliveryPlannable->planable_type} to delivery plan",
            properties: [
                'delivery_plan_id' => $deliveryPlannable->delivery_plan_id,
                'planable_type' => $deliveryPlannable->planable_type,
                'planable_id' => $deliveryPlannable->planable_id,
                'role' => $deliveryPlannable->role,
            ],
        );
    }
}
