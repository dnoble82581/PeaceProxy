<?php

namespace App\Events\Demand;

use App\Models\Demand;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DemandCreatedEvent implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(public Demand $demand)
    {
    }

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel("private.negotiation.{$this->demand->tenant_id}.{$this->demand->negotiation_id}");
    }

    public function broadcastAs(): string
    {
        return 'DemandCreated';
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->demand->id,
            'negotiation_id' => $this->demand->negotiation_id,
            'subject_id' => $this->demand->subject_id,
            'title' => $this->demand->title,
            'content' => $this->demand->content,
            'category' => $this->demand->category,
            'status' => $this->demand->status,
            'priority_level' => $this->demand->priority_level,
            'channel' => $this->demand->channel,
            'deadline_date' => $this->demand->deadline_date,
            'deadline_time' => $this->demand->deadline_time,
            'created_at' => $this->demand->created_at,
        ];
    }
}
