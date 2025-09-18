<?php

namespace App\Events\Message;

use App\Models\Message;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageReactionChangedEvent implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(public Message $message, public ?string $emoji, public User $reactor)
    {
    }


    public function broadcastOn(): array
    {
        // notify the message sender only
        return [new PrivateChannel('users.'.$this->message->user_id)];
    }


    public function broadcastAs(): string
    {
        return 'MessageReactionChanged';
    }


    public function broadcastWith(): array
    {
        return [
            'message_id' => $this->message->id,
            'emoji' => $this->emoji,
            'reactor' => [
                'id' => $this->reactor->id,
                'name' => $this->reactor->name,
            ],
        ];
    }
}
