<?php

namespace App\Events\DeliveryPlan;

use App\Models\DeliveryPlan;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DeliveryPlanCreatedEvent implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(public DeliveryPlan $deliveryPlan)
    {
    }

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel("private.negotiation.{$this->deliveryPlan->tenant_id}.{$this->deliveryPlan->negotiation_id}");
    }

    public function broadcastAs(): string
    {
        return 'DeliveryPlanCreated';
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->deliveryPlan->id,
            'negotiation_id' => $this->deliveryPlan->negotiation_id,
            'tenant_id' => $this->deliveryPlan->tenant_id,
            'title' => $this->deliveryPlan->title,
            'summary' => $this->deliveryPlan->summary,
            'category' => $this->deliveryPlan->category,
            'status' => $this->deliveryPlan->status,
            'scheduled_at' => $this->deliveryPlan->scheduled_at,
            'window_starts_at' => $this->deliveryPlan->window_starts_at,
            'window_ends_at' => $this->deliveryPlan->window_ends_at,
            'location_name' => $this->deliveryPlan->location_name,
            'location_address' => $this->deliveryPlan->location_address,
            'created_by' => $this->deliveryPlan->created_by,
            'updated_by' => $this->deliveryPlan->updated_by,
            'created_at' => $this->deliveryPlan->created_at,
        ];
    }
}
