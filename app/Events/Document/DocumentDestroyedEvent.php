<?php

namespace App\Events\Document;

use App\Support\Channels\Subject;
use App\Support\EventNames\SubjectEventNames;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DocumentDestroyedEvent implements ShouldBroadcastNow
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
            new PrivateChannel(Subject::subjectDocument($this->subjectId))
        ];
    }

    public function broadcastAs(): string
    {
        return SubjectEventNames::DOCUMENT_DELETED;
    }

    public function broadcastWith(): array
    {
        $documents = \App\Models\Document::where('documentable_id', $this->subjectId)
            ->select(['id', 'name', 'file_type', 'file_size', 'category', 'description'])
            ->get()
            ->toArray(); // Convert the collection into an array

        return [
            'subjectId' => $this->subjectId,
            'documents' => $documents, // Now documents are properly serialized
            'details' => $this->details,
        ];
    }
}
