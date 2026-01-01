<?php

namespace App\Events\Note;

use App\Support\Channels\Negotiation as NegotiationChannels;
use App\Support\EventNames\NegotiationEventNames;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NoteUpdatedEvent implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(public int $noteId, public int $negotiationId)
    {
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel(NegotiationChannels::negotiation($this->negotiationId)),
        ];
    }

    public function broadcastAs(): string
    {
        return NegotiationEventNames::NOTE_UPDATED;
    }

    public function broadcastWith(): array
    {
        return [
            'noteId' => $this->noteId,
            'negotiationId' => $this->negotiationId,
        ];
    }
}
