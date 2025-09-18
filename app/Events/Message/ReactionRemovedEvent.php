<?php

namespace App\Events\Message;

use App\Models\MessageReaction;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReactionRemovedEvent implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public MessageReaction $messageReaction)
    {
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        // Broadcast to both the negotiation-wide channel and the specific conversation channel
        return [
            new PresenceChannel("negotiation.{$this->messageReaction->negotiation_id}"),
            new PrivateChannel("conversation.{$this->messageReaction->message->conversation_id}"),
        ];
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        $message = $this->messageReaction->message;
        $conversation = $message->conversation;
        $user = $this->messageReaction->user;

        return [
            'message_id' => $message->id,
            'conversation_id' => $conversation->id,
            'conversation_name' => $conversation->name,
            'reaction_id' => $this->messageReaction->id,
            'reaction_type' => $this->messageReaction->reaction_type,
            'user_id' => $user->id,
            'user_name' => $user->name,
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'ReactionRemoved';
    }
}
