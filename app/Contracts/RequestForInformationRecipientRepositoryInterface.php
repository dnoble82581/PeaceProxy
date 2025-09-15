<?php

namespace App\Contracts;

use App\Models\RequestForInformationRecipient;
use Illuminate\Database\Eloquent\Collection;

interface RequestForInformationRecipientRepositoryInterface
{
    public function createRecipient(array $data): RequestForInformationRecipient;

    public function getRecipient(int $id): ?RequestForInformationRecipient;

    public function getRecipients(): Collection;

    public function getRecipientsByRfiId(int $rfiId): Collection;

    public function getRecipientByRfiIdAndUserId(int $rfiId, int $userId): ?RequestForInformationRecipient;

    public function updateRecipient(int $id, array $data): ?RequestForInformationRecipient;

    public function updateReadStatus(int $id, bool $isRead): ?RequestForInformationRecipient;

    public function deleteRecipient(int $id): ?RequestForInformationRecipient;
}
