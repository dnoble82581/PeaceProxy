<?php

namespace App\Events\Trigger;

use App\Support\Channels\Negotiation;
use App\Support\EventNames\NegotiationEventNames;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TriggerUpdatedEvent implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(public int $negotiationId, public int $triggerId)
    {
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel(Negotiation::negotiationTriggers($this->negotiationId)),
        ];
    }

    public function broadcastAs()
    {
        return NegotiationEventNames::TRIGGER_UPDATED;
    }

    public function broadcastWith()
    {
        return [
            'negotiationId' => $this->negotiationId,
            'triggerId' => $this->triggerId,
        ];
    }
}
