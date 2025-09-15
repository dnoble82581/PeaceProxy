<?php

namespace App\Services\RequestForInformationReply;

use App\Contracts\RequestForInformationReplyRepositoryInterface;
use App\DTOs\RequestForInformationReply\RequestForInformationReplyDTO;
use App\Events\Rfi\RfiReplyPostedEvent;
use App\Models\RequestForInformationReply;

class RequestForInformationReplyCreationService
{
    public function __construct(protected RequestForInformationReplyRepositoryInterface $replyRepository)
    {
    }

    public function createReply(RequestForInformationReplyDTO $replyDTO)
    {
        $reply = $this->replyRepository->createReply($replyDTO->toArray());

        $this->addLogEntry($reply);

        // Dispatch event
        event(new RfiReplyPostedEvent($reply));

        return $reply;
    }

    private function addLogEntry(RequestForInformationReply $reply): void
    {
        $user = auth()->user();

        app(\App\Services\Log\LogService::class)->writeAsync(
            tenantId: tenant()->id,
            event: 'rfi.reply.created',
            headline: "{$user->name} replied to a request for information",
            about: $reply->rfi,      // loggable target (the RFI, not the reply)
            by: $user,               // actor
            description: str($reply->content)->limit(140),
            properties: [
                'request_for_information_id' => $reply->request_for_information_id,
            ],
        );
    }
}
