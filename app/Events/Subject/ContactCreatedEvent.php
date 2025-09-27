<?php

namespace App\Events\Subject;

use App\Support\Channels\Subject;
use App\Support\EventNames\SubjectEventNames;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ContactCreatedEvent implements ShouldBroadcastNow
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
            new PrivateChannel(Subject::subjectContact($this->subjectId)),
        ];
    }

    public function broadcastAs(): string
    {
        return SubjectEventNames::CONTACT_CREATED;
    }

    public function broadcastWith(): array
    {
        return [
            'contactId' => $this->contactId,
            'subjectId' => $this->subjectId,
        ];
    }
}
