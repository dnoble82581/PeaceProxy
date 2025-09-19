<?php

namespace App\Services\DeliveryPlan;

use App\Contracts\DeliveryPlanRepositoryInterface;
use App\DTOs\DeliveryPlan\DeliveryPlanDTO;
use App\Models\DeliveryPlan;

class DeliveryPlanCreationService
{
    public function __construct(protected DeliveryPlanRepositoryInterface $deliveryPlanRepository)
    {
    }

    public function createDeliveryPlan(DeliveryPlanDTO $deliveryPlanDTO)
    {
        $deliveryPlan = $this->deliveryPlanRepository->createDeliveryPlan($deliveryPlanDTO->toArray());

        $this->addLogEntry($deliveryPlan);

        // Event could be added here if needed
        // event(new DeliveryPlanCreatedEvent($deliveryPlan));

        return $deliveryPlan;
    }

    private function addLogEntry(DeliveryPlan $deliveryPlan): void
    {
        $user = auth()->user();

        app(\App\Services\Log\LogService::class)->writeAsync(
            tenantId: tenant()->id,
            event: 'delivery_plan.created',
            headline: "{$user->name} created a delivery plan",
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
