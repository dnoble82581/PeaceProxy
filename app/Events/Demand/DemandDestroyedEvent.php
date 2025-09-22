<?php

namespace App\Events\Demand;

use App\Models\Demand;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DemandDestroyedEvent implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(public Demand $demand)
    {
    }

    public function broadcastOn()
    {
        return new PrivateChannel("private.negotiation.{$this->demand->tenant_id}.{$this->demand->negotiation_id}");
    }

    public function broadcastAs()
    {
        return 'DemandDestroyed';
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->demand->id,
            'negotiation_id' => $this->demand->negotiation_id,
            'subject_id' => $this->demand->subject_id,
            'details' => [
                'title' => $this->demand->title ?? 'Demand',
                'createdBy' => optional($this->demand->createdBy)->name ?? 'Someone',
                'subjectName' => optional($this->demand->subject)->name ?? 'the subject',
            ],
        ];
    }
}
