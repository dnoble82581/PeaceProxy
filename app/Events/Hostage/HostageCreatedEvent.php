<?php

namespace App\Events\Hostage;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class HostageCreatedEvent implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(public \App\Models\Hostage $hostage)
    {
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel("private.negotiation.{$this->hostage->tenant_id}.{$this->hostage->negotiation_id}"),
        ];
    }

    public function broadcastAs(): string
    {
        return 'HostageCreated';
    }

    public function broadcastWith(): array
    {
        return [
            'hostageId' => $this->hostage->id,
        ];
    }
}
