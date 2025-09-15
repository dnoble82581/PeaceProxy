<?php

namespace App\Contracts;

use App\Models\RequestForInformationReply;
use Illuminate\Database\Eloquent\Collection;

interface RequestForInformationReplyRepositoryInterface
{
    public function createReply(array $data): RequestForInformationReply;

    public function getReply(int $id): ?RequestForInformationReply;

    public function getReplies(): Collection;

    public function getRepliesByRfiId(int $rfiId): Collection;

    public function updateReply(int $id, array $data): ?RequestForInformationReply;

    public function deleteReply(int $id): ?RequestForInformationReply;
}
