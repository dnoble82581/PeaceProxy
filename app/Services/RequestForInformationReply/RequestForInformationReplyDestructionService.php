<?php

namespace App\Services\RequestForInformationReply;

use App\Contracts\RequestForInformationReplyRepositoryInterface;
use App\Models\RequestForInformationReply;

class RequestForInformationReplyDestructionService
{
    public function __construct(protected RequestForInformationReplyRepositoryInterface $replyRepository)
    {
    }

    public function deleteReply($replyId)
    {
        $reply = $this->replyRepository->getReply($replyId);

        if (!$reply) {
            return null;
        }

        $this->addLogEntry($reply);

        // No specific event for reply deletion - the UI can handle this via the reply list

        return $this->replyRepository->deleteReply($replyId);
    }

    private function addLogEntry(RequestForInformationReply $reply): void
    {
        $user = auth()->user();

        app(\App\Services\Log\LogService::class)->writeAsync(
            tenantId: tenant()->id,
            event: 'rfi.reply.deleted',
            headline: "{$user->name} deleted a reply to a request for information",
            about: $reply->rfi,      // loggable target (the RFI, not the reply)
            by: $user,               // actor
            description: str($reply->content)->limit(140),
            properties: [
                'request_for_information_id' => $reply->request_for_information_id,
            ],
        );
    }
}
