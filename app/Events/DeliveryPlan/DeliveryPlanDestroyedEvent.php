<?php

namespace App\Events\DeliveryPlan;

use App\Models\DeliveryPlan;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DeliveryPlanDestroyedEvent implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    private int $deliveryPlanId;
    private int $tenantId;
    private int $negotiationId;

    public function __construct(DeliveryPlan $deliveryPlan)
    {
        // Store the IDs before the model is deleted
        $this->deliveryPlanId = $deliveryPlan->id;
        $this->tenantId = $deliveryPlan->tenant_id;
        $this->negotiationId = $deliveryPlan->negotiation_id;
    }

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel("private.negotiation.{$this->tenantId}.{$this->negotiationId}");
    }

    public function broadcastAs(): string
    {
        return 'DeliveryPlanDestroyed';
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->deliveryPlanId,
            'negotiation_id' => $this->negotiationId,
            'tenant_id' => $this->tenantId,
        ];
    }
}
