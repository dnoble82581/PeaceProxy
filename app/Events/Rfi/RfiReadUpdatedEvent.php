<?php

namespace App\Events\Rfi;

use App\Models\RequestForInformationRecipient;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RfiReadUpdatedEvent implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(public RequestForInformationRecipient $recipient)
    {
    }

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel("tenants.{$this->recipient->tenant_id}.rfi.{$this->recipient->request_for_information_id}");
    }

    public function broadcastAs(): string
    {
        return 'RfiReadUpdated';
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->recipient->id,
            'tenant_id' => $this->recipient->tenant_id,
            'request_for_information_id' => $this->recipient->request_for_information_id,
            'user_id' => $this->recipient->user_id,
            'is_read' => $this->recipient->is_read,
            'read_at' => $this->recipient->read_at,
            'updated_at' => $this->recipient->updated_at,
        ];
    }
}
