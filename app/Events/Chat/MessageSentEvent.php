<?php

namespace App\Events\Chat;

use App\Models\Message;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSentEvent implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(public Message $message)
    {
        // Make sure we have what we need for broadcastWith()
        $this->message->loadMissing('conversation');
    }

    /**
     * Broadcast on BOTH:
     *  - negotiation presence channel (so people in other tabs/threads hear it)
     *  - conversation private channel (for the active thread)
     */
    public function broadcastOn(): array
    {
        $negotiationId = (int) $this->message->conversation->negotiation_id;
        $conversationId = (int) $this->message->conversation_id;

        return [
            new PresenceChannel("negotiation.{$negotiationId}"),
            new PrivateChannel("conversation.{$conversationId}"),
        ];
    }

    /**
     * Match Livewire’s dotted listener keys: ".MessageSent"
     */
    public function broadcastAs(): string
    {
        return 'MessageSent';
    }

    public function broadcastWith(): array
    {
        $conv = $this->message->conversation;

        return [
            'conversation_id' => (int) $this->message->conversation_id,
            'message_id' => (int) $this->message->id,
            'sender_id' => (int) $this->message->user_id,
            'negotiation_id' => (int) $conv->negotiation_id,
            'type' => (string) $conv->type,           // 'public' | 'private' | 'group'
            'conversation_name' => (string) ($conv->name ?? 'Public'),
        ];
    }
}
