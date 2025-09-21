<?php

namespace App\Events\Subject;

use App\Support\Channels\Subject;
use App\Support\EventNames\SubjectEventNames;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SubjectUpdatedEvent implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(public int $subjectId)
    {

    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel(Subject::subject($this->subjectId)),
        ];
    }

    public function broadcastAs(): string
    {
        return SubjectEventNames::SUBJECT_UPDATED;
    }

    public function broadcastWith(): array
    {
        return [
            'subjectId' => $this->subjectId,
        ];
    }
}
