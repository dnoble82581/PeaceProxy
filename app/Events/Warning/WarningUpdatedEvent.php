<?php

namespace App\Events\Warning;

use App\Support\Channels\Subject;
use App\Support\EventNames\SubjectEventNames;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WarningUpdatedEvent implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(public int $subjectId, public int $warningId)
    {
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel(Subject::subjectWarning($this->subjectId))
        ];
    }

    public function broadcastAs(): string
    {
        return SubjectEventNames::WARNING_UPDATED;
    }

    public function broadcastWith()
    {
        return [
            'subjectId' => $this->subjectId,
            'warningId' => $this->warningId,
        ];
    }
}
