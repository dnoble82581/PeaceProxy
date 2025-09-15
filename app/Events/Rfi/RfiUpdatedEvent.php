<?php

namespace App\Events\Rfi;

use App\Models\RequestForInformation;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RfiUpdatedEvent implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(public RequestForInformation $rfi)
    {
    }

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel("tenants.{$this->rfi->tenant_id}.rfi.{$this->rfi->id}");
    }

    public function broadcastAs(): string
    {
        return 'RfiUpdated';
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->rfi->id,
            'tenant_id' => $this->rfi->tenant_id,
            'negotiation_id' => $this->rfi->negotiation_id,
            'user_id' => $this->rfi->user_id,
            'title' => $this->rfi->title,
            'body' => $this->rfi->body,
            'status' => $this->rfi->status,
            'due_date' => $this->rfi->due_date,
            'updated_at' => $this->rfi->updated_at,
        ];
    }
}
