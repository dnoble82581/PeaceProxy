<?php

namespace App\Events\Mood;

use App\Models\moodLog;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MoodCreatedEvent implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(public MoodLog $mood)
    {
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('negotiation.'.$this->mood->negotiation_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'MoodCreated';
    }

    public function broadcastWith(): array
    {
        return [
            'mood' => [
                'id' => $this->mood->id,
                'value' => $this->mood->value,
                'description' => $this->mood->description,
                'created_at' => $this->mood->created_at->toDateTimeString(),
            ],
        ];
    }
}
