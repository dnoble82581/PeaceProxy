<?php

namespace App\Events\Rfi;

use App\Support\Channels\Negotiation;
use App\Support\EventNames\NegotiationEventNames;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RfiCreatedEvent implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(public int $negotiationId, public int $rfiId)
    {
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel(Negotiation::negotiationRfi($this->negotiationId)),
        ];
    }

    public function broadcastAs(): string
    {
        return NegotiationEventNames::RFI_CREATED;
    }

    public function broadcastWith()
    {
        return [
           'negotiationId' => $this->negotiationId,
            'rfiId' => $this->rfiId,
        ];
    }
}
