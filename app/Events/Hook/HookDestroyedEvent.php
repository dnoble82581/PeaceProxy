<?php

namespace App\Events\Hook;

use App\Support\Channels\Negotiation;
use App\Support\EventNames\NegotiationEventNames;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class HookDestroyedEvent implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(public int $negotiationId, public int $hookId)
    {
    }

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel(Negotiation::negotiationHook($this->negotiationId));
    }

    public function broadcastAs()
    {
        return NegotiationEventNames::HOOK_DELETED;
    }

    public function broadcastWith(): array
    {
        return [
            'negotiationId' => $this->negotiationId,
            'hookId' => $this->hookId,
        ];
    }
}
