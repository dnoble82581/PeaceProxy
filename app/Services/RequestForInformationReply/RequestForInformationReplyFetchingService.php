<?php

namespace App\Services\RequestForInformationReply;

use App\Contracts\RequestForInformationReplyRepositoryInterface;
use App\DTOs\RequestForInformationReply\RequestForInformationReplyDTO;
use App\Models\RequestForInformationReply;

class RequestForInformationReplyFetchingService
{
    public function __construct(protected RequestForInformationReplyRepositoryInterface $replyRepository)
    {
    }

    public function getReplyById($replyId)
    {
        return $this->replyRepository->getReply($replyId);
    }

    public function getAllReplies()
    {
        return $this->replyRepository->getReplies();
    }

    public function getRepliesByRfiId($rfiId)
    {
        return $this->replyRepository->getRepliesByRfiId($rfiId);
    }

    public function getRepliesByUserId($userId)
    {
        return RequestForInformationReply::where('user_id', $userId)->get();
    }

    public function getReplyDTO($replyId): ?RequestForInformationReplyDTO
    {
        $reply = $this->getReplyById($replyId);

        if (!$reply) {
            return null;
        }

        return RequestForInformationReplyDTO::fromArray($reply->toArray());
    }
}
