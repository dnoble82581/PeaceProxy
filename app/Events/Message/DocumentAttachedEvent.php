<?php

namespace App\Events\Message;

use App\Models\MessageDocument;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DocumentAttachedEvent implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public MessageDocument $messageDocument)
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
            new PresenceChannel("negotiation.{$this->messageDocument->negotiation_id}"),
            new PrivateChannel("conversation.{$this->messageDocument->message->conversation_id}"),
        ];
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        $message = $this->messageDocument->message;
        $document = $this->messageDocument->document;
        $conversation = $message->conversation;

        return [
            'message_id' => $message->id,
            'conversation_id' => $conversation->id,
            'conversation_name' => $conversation->name,
            'document_id' => $document->id,
            'document_name' => $document->name,
            'document_type' => $document->file_type,
            'document_size' => $document->file_size,
            'sender_id' => $message->user_id,
            'sender_name' => $message->user->name,
            'attached_at' => $this->messageDocument->created_at->toIso8601String(),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'DocumentAttached';
    }
}
