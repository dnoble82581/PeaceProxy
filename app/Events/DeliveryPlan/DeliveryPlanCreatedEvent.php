<?php

namespace App\Events\DeliveryPlan;

use App\Support\Channels\Negotiation;
use App\Support\EventNames\NegotiationEventNames;
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

    public function __construct(public int $negotiationId, public int $deliveryPlanId)
    {
    }

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel(Negotiation::negotiationDeliveryPlan($this->negotiationId));
    }

    public function broadcastAs(): string
    {
        return NegotiationEventNames::DELIVERY_PLAN_CREATED;
    }

    public function broadcastWith()
    {
        return [
            'deliveryPlanId' => $this->deliveryPlanId,
            'negotiationId' => $this->negotiationId,
        ];
    }
}
