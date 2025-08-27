<?php

namespace App\Events\Pin;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ObjectiveUnpinnedEvent implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public int $tenantId,
        public int $objectiveId
    ) {
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel("tenants.{$this->tenantId}.notifications"),
        ];
    }

    public function broadcastAs()
    {
        return 'ObjectiveUnpinned';
    }
}
