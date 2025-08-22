<?php

namespace App\Events\Conversation;

use App\Models\Conversation;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ConversationClosedEvent implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(public Conversation $conversation)
    {
    }

    public function broadcastOn(): array
    {
        return [
            new PresenceChannel("negotiation.{$this->conversation->negotiation_id}"),
        ];
    }

    public function broadcastAs()
    {
        return 'ConversationClosed';
    }

    public function broadcastWith()
    {
        return [
            'conversationId' => $this->conversation->id,
        ];
    }
}
