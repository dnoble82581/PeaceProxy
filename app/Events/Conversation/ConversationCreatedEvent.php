<?php

namespace App\Events\Conversation;

use App\Models\Conversation;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ConversationCreatedEvent implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(public Conversation $conversation)
    {
        Log::info('ConversationCreatedEvent constructed for conversation ID: '.$this->conversation->id);
    }

    public function broadcastOn(): array
    {
        return [
            new PresenceChannel("negotiation.{$this->conversation->negotiation_id}"),
        ];
    }

    public function broadcastAs()
    {
        return 'ConversationCreated';
    }

    public function broadcastWith(): array
    {
        return [
            'conversation_id' => $this->conversation->id, // ← was conversationId
        ];
    }
}
