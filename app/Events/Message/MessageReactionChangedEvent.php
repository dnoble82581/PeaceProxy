<?php

namespace App\Events\Message;

use App\Models\Message;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageReactionChangedEvent implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(public Message $message, public ?string $emoji, public ?string $oldEmoji, public User $reactor, public ?int $oldCount, public ?int $newCount)
    {
    }

    public function broadcastOn(): array
    {
        // Get all users in the negotiation
        $negotiationId = $this->message->negotiation_id;
        $tenantId = $this->message->tenant_id;

        // Broadcast to the negotiation channel for all users
        $channels = [
            new PrivateChannel("private.negotiation.{$tenantId}.{$negotiationId}"),
        ];

        // Also broadcast to the reactor's private channel
        $channels[] = new PrivateChannel('private.users.'.$this->reactor->id);

        return $channels;
    }

    public function broadcastAs(): string
    {
        return 'MessageReactionChanged';
    }

    public function broadcastWith(): array
    {
        return [
            'message_id' => $this->message->id,
            'newEmoji' => $this->emoji, // null if user cleared
            'newEmojiCount' => $this->emoji
                ? $this->newCount
                : null,
            'oldEmoji' => $this->oldEmoji, // the one being toggled off
            'oldEmojiCount' => $this->oldEmoji
                ? $this->oldCount
                : null,
            'reactor' => [
                'id' => $this->reactor->id,
                'name' => $this->reactor->name,
            ],
        ];
    }
}
