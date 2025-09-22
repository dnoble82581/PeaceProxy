<?php

namespace App\Events\Objective;

use App\Support\Channels\Negotiation;
use App\Support\EventNames\NegotiationEventNames;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ObjectiveDeletedEvent implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(public array $data)
    {
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel(Negotiation::negotiationObjective($this->data['negotiationId']))
        ];
    }

    public function broadcastAs()
    {
        return NegotiationEventNames::OBJECTIVE_DELETED;
    }

    public function broadcastWith(): array
    {
        return [
            'objectiveId' => $this->data['objectiveId'],
            'negotiationId' => $this->data['negotiationId'],
            'actorId' => $this->data['actorId'],
            'actorName' => $this->data['actorName'],
            'priority' => $this->data['priority'],
            'objectiveLabel' => $this->data['objectiveLabel'],
        ];
    }
}
