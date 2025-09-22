<?php

namespace App\Events\Document;

use App\Support\Channels\Subject;
use App\Support\EventNames\SubjectEventNames;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DocumentCreatedEvent implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(public int $subjectId, public int $documentId)
    {
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel(Subject::subjectDocument($this->subjectId))
        ];
    }

    public function broadcastAs(): string
    {
        return SubjectEventNames::DOCUMENT_CREATED;
    }

    public function broadcastWith(): array
    {
        return [
            'documentId' => $this->documentId,
            'subjectId' => $this->subjectId,
        ];
    }
}
