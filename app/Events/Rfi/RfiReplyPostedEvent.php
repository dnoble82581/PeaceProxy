<?php

namespace App\Events\Rfi;

use App\Models\RequestForInformationReply;
use App\Support\Channels\Negotiation;
use App\Support\EventNames\NegotiationEventNames;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RfiReplyPostedEvent implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(public RequestForInformationReply $reply, public int $negotiationId)
    {
    }

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel(Negotiation::negotiationRfi($this->negotiationId));
    }

    public function broadcastAs(): string
    {
        return NegotiationEventNames::RFI_RESPONDED;
    }

    public function broadcastWith()
    {
        $rfi = \App\Models\RequestForInformation::find($this->reply->request_for_information_id);

        return [
            'id' => $this->reply->id,
            'tenant_id' => $this->reply->tenant_id,
            'request_for_information_id' => $this->reply->request_for_information_id,
            'replies_count' => $rfi->replies->count(),
            'user_id' => $this->reply->user_id,
            'body' => $this->reply->body,
            'is_read' => $this->reply->is_read,
            'created_at' => $this->reply->created_at,
        ];
    }
}
