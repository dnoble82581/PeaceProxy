<?php

namespace App\Services\RequestForInformationReply;

use App\Contracts\RequestForInformationReplyRepositoryInterface;
use App\DTOs\RequestForInformationReply\RequestForInformationReplyDTO;
use App\Models\RequestForInformationReply;
use Illuminate\Database\Eloquent\Collection;

class RequestForInformationReplyFetchingService
{
    public function __construct(protected RequestForInformationReplyRepositoryInterface $replyRepository)
    {
    }

    public function getAllReplies(): Collection
    {
        return $this->replyRepository->getReplies();
    }

    /**
     * @return Collection<int, RequestForInformationReply>
     */
    public function getRepliesByRfiId(int $rfiId): Collection
    {
        return $this->replyRepository->getRepliesByRfiId($rfiId);
    }

    /**
     * @return Collection<int, RequestForInformationReply>
     */
    public function getRepliesByUserId(int $userId): Collection
    {
        return RequestForInformationReply::where('user_id', $userId)->get();
    }

    public function getReplyDTO(int $replyId): ?RequestForInformationReplyDTO
    {
        $reply = $this->getReplyById($replyId);

        if (! $reply) {
            return null;
        }

        return RequestForInformationReplyDTO::fromArray($reply->toArray());
    }

    public function getReplyById(int $replyId): RequestforInformationReply
    {
        return $this->replyRepository->getReply($replyId);
    }
}
