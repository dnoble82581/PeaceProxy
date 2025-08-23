<?php

namespace App\Events\Pin;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NoteUnpinnedEvent implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(public int $tenantId, public int $noteId)
    {
    }

    public function broadcastOn()
    {
        return new PrivateChannel("tenants.{$this->tenantId}.notifications");
    }
    public function broadcastAs()
    {
        return 'NoteUnpinned';
    }
}
