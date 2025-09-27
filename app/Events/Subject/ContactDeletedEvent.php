<?php

namespace App\Events\Subject;

use App\Support\Channels\Subject;
use App\Support\EventNames\SubjectEventNames;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ContactDeletedEvent implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(public array $data)
    {
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel(Subject::subjectContact($this->data['subjectId'])),
        ];
    }

    public function broadcastAs()
    {
        return SubjectEventNames::CONTACT_DELETED;
    }

    public function broadcastWith()
    {
        return [
            'subjectId' => $this->data['subjectId'],
            'contactId' => $this->data['contactPointId'],
        ];
    }
}
