<?php

namespace App\Events\Warrant;

use App\Support\Channels\Subject;
use App\Support\EventNames\SubjectEventNames;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WarrantDeletedEvent implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(public int $subjectId, public array $details)
    {
    }

    public function broadcastOn(): array
    {

        return [
            new PrivateChannel(Subject::subjectWarrant($this->subjectId)),
        ];
    }

    public function broadcastAs(): string
    {
        return SubjectEventNames::WARRANT_DELETED;
    }

    public function broadcastWith(): array
    {
        return [
            'subjectId' => $this->subjectId,
            'details' => $this->details,
        ];
    }
}
