<?php

namespace App\Services\DeliveryPlan;

use App\Contracts\DeliveryPlanRepositoryInterface;
use App\Models\DeliveryPlan;

class DeliveryPlanDestructionService
{
    public function __construct(protected DeliveryPlanRepositoryInterface $deliveryPlanRepository)
    {
    }

    public function deleteDeliveryPlan($id)
    {
        $deliveryPlan = $this->deliveryPlanRepository->getDeliveryPlan($id);

        if (!$deliveryPlan) {
            return null;
        }

        $this->addLogEntry($deliveryPlan);

        // Event could be added here if needed
        // event(new DeliveryPlanDeletedEvent($deliveryPlan));

        return $this->deliveryPlanRepository->deleteDeliveryPlan($id);
    }

    private function addLogEntry(DeliveryPlan $deliveryPlan): void
    {
        $user = auth()->user();

        app(\App\Services\Log\LogService::class)->writeAsync(
            tenantId: tenant()->id,
            event: 'delivery_plan.deleted',
            headline: "{$user->name} deleted a delivery plan",
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
