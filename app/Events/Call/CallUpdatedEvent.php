<?php

namespace App\Events\Call;

use App\Models\Call;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class CallUpdatedEvent implements ShouldBroadcast
{
    public function __construct(public Call $call)
    {
    }

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('tenants.'.$this->call->tenant_id.'.calls');
    }

    public function broadcastAs(): string
    {
        return 'call.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->call->id,
            'call_sid' => $this->call->call_sid,
            'status' => $this->call->status,
            'answered_by' => $this->call->answered_by,
            'duration_seconds' => $this->call->duration_seconds,
            'from' => $this->call->from_e164,
            'to' => $this->call->to_e164,
            'timestamps' => [
                'queued_at' => optional($this->call->queued_at)->toIso8601ZuluString(),
                'ringing_at' => optional($this->call->ringing_at)->toIso8601ZuluString(),
                'answered_at' => optional($this->call->answered_at)->toIso8601ZuluString(),
                'completed_at' => optional($this->call->completed_at)->toIso8601ZuluString(),
            ],
        ];
    }
}
