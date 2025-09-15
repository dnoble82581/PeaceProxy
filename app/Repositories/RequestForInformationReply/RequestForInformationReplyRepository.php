<?php

namespace App\Repositories\RequestForInformationReply;

use App\Contracts\RequestForInformationReplyRepositoryInterface;
use App\Models\RequestForInformationReply;
use Illuminate\Database\Eloquent\Collection;

class RequestForInformationReplyRepository implements RequestForInformationReplyRepositoryInterface
{
    public function createReply(array $data): RequestForInformationReply
    {
        return RequestForInformationReply::create($data);
    }

    public function getReplies(): Collection
    {
        return RequestForInformationReply::all();
    }

    public function getRepliesByRfiId(int $rfiId): Collection
    {
        return RequestForInformationReply::where('request_for_information_id', $rfiId)->get();
    }

    public function updateReply(int $id, array $data): ?RequestForInformationReply
    {
        $reply = $this->getReply($id);
        if ($reply) {
            $reply->update($data);
        }
        return $reply;
    }

    public function getReply(int $id): ?RequestForInformationReply
    {
        return RequestForInformationReply::find($id);
    }

    public function deleteReply(int $id): ?RequestForInformationReply
    {
        $reply = $this->getReply($id);
        if ($reply) {
            $reply->delete();
        }
        return $reply;
    }
}
