<?php

namespace App\Events\Subject;

use App\Support\Channels\Subject;
use App\Support\EventNames\SubjectEventNames;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ContactUpdatedEvent implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(public int $subjectId, public int $contactId)
    {
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel(Subject::subjectContact($this->subjectId))
        ];
    }

    public function broadcastAs()
    {
        return SubjectEventNames::CONTACT_UPDATED;
    }

    public function broadcastWith()
    {
        return [
            'contactId' => $this->contactId,
            'subjectId' => $this->subjectId,
        ];
    }
}
