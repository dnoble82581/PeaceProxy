<?php

namespace App\Events\Demand;

use App\Support\Channels\Negotiation;
use App\Support\EventNames\NegotiationEventNames;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DemandDestroyedEvent implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(public array $data)
    {
    }

    public function broadcastOn()
    {
        return new PrivateChannel(Negotiation::negotiationDemand($this->data['negotiationId']));
    }

    public function broadcastAs()
    {
        return NegotiationEventNames::DEMAND_DELETED;
    }

    public function broadcastWith()
    {
        return [
            'negotiationId' => $this->data['negotiationId'],
            'demandId' => $this->data['id'],
            'actorId' => $this->data['actorId'],
            'category' => $this->data['category'],
            'status' => $this->data['status'],
            'title' => $this->data['title'],
        ];
    }
}
