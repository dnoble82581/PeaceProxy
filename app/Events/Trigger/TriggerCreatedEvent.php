<?php

namespace App\Events\Trigger;

use App\Models\Trigger;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TriggerCreatedEvent implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(public Trigger $trigger)
    {
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('negotiation.'.$this->trigger->negotiation_id)
        ];
    }

    public function broadcastAs()
    {
        return 'TriggerCreated';
    }

    public function broadcastWith()
    {
        return [
            'trigger' => $this->trigger->id,
        ];
    }
}
