<?php

namespace App\Events\Document;

use App\Support\Channels\Negotiation;
use App\Support\EventNames\NegotiationEventNames;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NegotiationDocumentCreatedEvent implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(public int $negotiationId, public int $documentId)
    {
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel(Negotiation::negotiationDocument($this->negotiationId))
        ];
    }

    public function broadcastAs()
    {
        return NegotiationEventNames::DOCUMENT_UPLOADED;
    }

    public function broadcastWith()
    {
        return [
            'negotiationId' => $this->negotiationId,
            'documentId' => $this->documentId,
        ];
    }
}
