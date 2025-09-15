<?php

namespace App\Services\RequestForInformationReply;

use App\Contracts\RequestForInformationReplyRepositoryInterface;
use App\DTOs\RequestForInformationReply\RequestForInformationReplyDTO;
use App\Events\Rfi\RfiReplyPostedEvent;
use App\Models\RequestForInformationReply;

class RequestForInformationReplyUpdateService
{
    public function __construct(protected RequestForInformationReplyRepositoryInterface $replyRepository)
    {
    }

    public function updateReply($replyId, RequestForInformationReplyDTO $replyDTO)
    {
        $reply = $this->replyRepository->updateReply($replyId, $replyDTO->toArray());

        $this->addLogEntry($reply);

        // Dispatch event - reusing the RfiReplyPostedEvent since it's essentially the same data
        event(new RfiReplyPostedEvent($reply));

        return $reply;
    }

    private function addLogEntry(RequestForInformationReply $reply): void
    {
        $user = auth()->user();

        app(\App\Services\Log\LogService::class)->writeAsync(
            tenantId: tenant()->id,
            event: 'rfi.reply.updated',
            headline: "{$user->name} updated a reply to a request for information",
            about: $reply->rfi,      // loggable target (the RFI, not the reply)
            by: $user,               // actor
            description: str($reply->content)->limit(140),
            properties: [
                'request_for_information_id' => $reply->request_for_information_id,
            ],
        );
    }
}
