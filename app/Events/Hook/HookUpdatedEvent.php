<?php

namespace App\Events\Hook;

use App\Models\Hook;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class HookUpdatedEvent implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(public Hook $hook)
    {
    }

    public function broadcastOn(): PrivateChannel
    {

        return new PrivateChannel("private.negotiation.{$this->hook->tenant_id}.{$this->hook->negotiation_id}");
    }

    public function broadcastAs()
    {
        return 'HookUpdated';
    }

    public function broadcastWith()
    {
        return [
            'hook' => $this->hook->id,
        ];
    }
}
