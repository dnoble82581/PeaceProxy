<?php

namespace App\Events\Objective;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ObjectiveCreatedEvent implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(public \App\Models\Objective $objective)
    {
    }

    public function broadcastOn(): array
    {

        return [
            new PrivateChannel('private.negotiation.'.$this->objective->tenant_id.'.'.$this->objective->negotiation_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'ObjectiveCreated';
    }

    public function broadcastWith(): array
    {
        return [
            'objectiveId' => $this->objective->id,
        ];
    }
}
