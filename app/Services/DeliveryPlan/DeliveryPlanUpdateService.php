<?php

namespace App\Services\DeliveryPlan;

use App\Contracts\DeliveryPlanRepositoryInterface;
use App\DTOs\DeliveryPlan\DeliveryPlanDTO;
use App\Models\DeliveryPlan;

class DeliveryPlanUpdateService
{
    public function __construct(protected DeliveryPlanRepositoryInterface $deliveryPlanRepository)
    {
    }

    public function updateDeliveryPlan(DeliveryPlanDTO $deliveryPlanDTO, $id)
    {
        $deliveryPlan = $this->deliveryPlanRepository->updateDeliveryPlan($id, $deliveryPlanDTO->toArray());

        $this->addLogEntry($deliveryPlan);

        // Event could be added here if needed
        // event(new DeliveryPlanUpdatedEvent($deliveryPlan));

        return $deliveryPlan;
    }

    private function addLogEntry(DeliveryPlan $deliveryPlan): void
    {
        $user = auth()->user();

        app(\App\Services\Log\LogService::class)->writeAsync(
            tenantId: tenant()->id,
            event: 'delivery_plan.updated',
            headline: "{$user->name} updated a delivery plan",
            about: $deliveryPlan,      // loggable target
            by: $user,                  // actor
            description: str($deliveryPlan->title)->limit(140),
            properties: [
                'negotiation_id' => $deliveryPlan->negotiation_id,
                'category' => $deliveryPlan->category,
                'status' => $deliveryPlan->status,
            ],
        );
    }
}
