<?php

namespace App\Events\Document;

use App\Support\Channels\Negotiation;
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

    public function __construct(public int $subjectId, public int $negotiationId, public array $details)
    {
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel(Subject::subjectDocument($this->subjectId)),
            new PrivateChannel(Negotiation::negotiationDocument($this->negotiationId)),
        ];
    }

    public function broadcastAs(): string
    {
        // Both SubjectEventNames::DOCUMENT_DELETED and NegotiationEventNames::DOCUMENT_DELETED resolve to the same string
        // so broadcasting once with this alias will satisfy listeners on both channels returned above.
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
            'negotiationId' => $this->negotiationId,
            'documents' => $documents, // Now documents are properly serialized
            'details' => $this->details,
        ];
    }
}
