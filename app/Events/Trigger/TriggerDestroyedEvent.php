<?php

namespace App\Events\Trigger;

use App\Models\Trigger;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TriggerDestroyedEvent implements ShouldBroadcastNow
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
            new PrivateChannel("private.negotiation.{$this->trigger->tenant_id}.{$this->trigger->negotiation_id}"),
        ];
    }

    public function broadcastAs()
    {
        return 'TriggerDestroyed';
    }

    public function broadcastWith(): array
    {
        return [
            'triggerId' => $this->trigger->id,
            'details' => [
                'title' => $this->trigger->title ?? 'Trigger',
                'createdBy' => optional($this->trigger->user)->name ?? 'Someone',
                'subjectName' => optional($this->trigger->subject)->name ?? 'the subject',
            ],
        ];
    }
}
