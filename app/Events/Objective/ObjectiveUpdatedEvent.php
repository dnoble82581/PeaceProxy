<?php

namespace App\Events\Objective;

use App\Support\Channels\Negotiation;
use App\Support\EventNames\NegotiationEventNames;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ObjectiveUpdatedEvent implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(public int $negotiationId, public int $objectiveId)
    {
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel(Negotiation::negotiationObjective($this->negotiationId)),
        ];
    }

    public function broadcastAs()
    {
        return NegotiationEventNames::OBJECTIVE_UPDATED;
    }

    public function broadcastWith()
    {
        return [
            'objectiveId' => $this->objectiveId,
            'negotiationId' => $this->negotiationId,
        ];
    }
}
