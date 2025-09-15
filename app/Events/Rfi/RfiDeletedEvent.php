<?php

namespace App\Events\Rfi;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RfiDeletedEvent implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public int $rfiId,
        public int $tenantId,
        public int $negotiationId
    ) {
    }

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel("tenants.{$this->tenantId}.rfi.{$this->rfiId}");
    }

    public function broadcastAs(): string
    {
        return 'RfiDeleted';
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->rfiId,
            'tenant_id' => $this->tenantId,
            'negotiation_id' => $this->negotiationId,
        ];
    }
}
