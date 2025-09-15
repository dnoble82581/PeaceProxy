<?php

namespace App\Repositories\RequestForInformationRecipient;

use App\Contracts\RequestForInformationRecipientRepositoryInterface;
use App\Models\RequestForInformationRecipient;
use Illuminate\Database\Eloquent\Collection;

class RequestForInformationRecipientRepository implements RequestForInformationRecipientRepositoryInterface
{
    public function createRecipient(array $data): RequestForInformationRecipient
    {
        return RequestForInformationRecipient::create($data);
    }

    public function getRecipients(): Collection
    {
        return RequestForInformationRecipient::all();
    }

    public function getRecipientsByRfiId(int $rfiId): Collection
    {
        return RequestForInformationRecipient::where('request_for_information_id', $rfiId)->get();
    }

    public function getRecipientByRfiIdAndUserId(int $rfiId, int $userId): ?RequestForInformationRecipient
    {
        return RequestForInformationRecipient::where('request_for_information_id', $rfiId)
            ->where('user_id', $userId)
            ->first();
    }

    public function updateRecipient(int $id, array $data): ?RequestForInformationRecipient
    {
        $recipient = $this->getRecipient($id);
        if ($recipient) {
            $recipient->update($data);
        }
        return $recipient;
    }

    public function updateReadStatus(int $id, bool $isRead): ?RequestForInformationRecipient
    {
        $recipient = $this->getRecipient($id);
        if ($recipient) {
            $recipient->update(['is_read' => $isRead]);
        }
        return $recipient;
    }

    public function getRecipient(int $id): ?RequestForInformationRecipient
    {
        return RequestForInformationRecipient::find($id);
    }

    public function deleteRecipient(int $id): ?RequestForInformationRecipient
    {
        $recipient = $this->getRecipient($id);
        if ($recipient) {
            $recipient->delete();
        }
        return $recipient;
    }
}
