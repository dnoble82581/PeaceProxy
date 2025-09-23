<?php

namespace App\Events\Demand;

use App\Support\Channels\Negotiation;
use App\Support\EventNames\NegotiationEventNames;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DemandUpdatedEvent implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(public int $negotiationId, public int $demandId)
    {
    }

    public function broadcastOn()
    {
        return new PrivateChannel(Negotiation::negotiationDemand($this->negotiationId));
    }

    public function broadcastAs()
    {
        return NegotiationEventNames::DEMAND_UPDATED;
    }

    public function broadcastWith()
    {
        return [
           'demandId' => $this->demandId,
            'negotiationId' => $this->negotiationId,
        ];
    }
}
