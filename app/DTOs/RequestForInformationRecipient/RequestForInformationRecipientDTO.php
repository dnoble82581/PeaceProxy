<?php

namespace App\DTOs\RequestForInformationRecipient;

use Carbon\Carbon;

class RequestForInformationRecipientDTO
{
    public function __construct(
        public ?int $id = null,
        public ?int $tenant_id = null,
        public ?int $request_for_information_id = null,
        public ?int $user_id = null,
        public ?bool $is_read = false,
        public ?Carbon $read_at = null,
        public ?Carbon $created_at = null,
        public ?Carbon $updated_at = null,
        public ?Carbon $deleted_at = null,
    ) {
    }

    public static function fromArray(array $data): RequestForInformationRecipientDTO
    {
        return new self(
            $data['id'] ?? null,
            $data['tenant_id'] ?? null,
            $data['request_for_information_id'] ?? null,
            $data['user_id'] ?? null,
            $data['is_read'] ?? false,
            isset($data['read_at']) ? Carbon::parse($data['read_at']) : null,
            isset($data['created_at']) ? Carbon::parse($data['created_at']) : null,
            isset($data['updated_at']) ? Carbon::parse($data['updated_at']) : null,
            isset($data['deleted_at']) ? Carbon::parse($data['deleted_at']) : null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'tenant_id' => $this->tenant_id,
            'request_for_information_id' => $this->request_for_information_id,
            'user_id' => $this->user_id,
            'is_read' => $this->is_read,
            'read_at' => $this->read_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ];
    }

    public function toJson($options = 0): string
    {
        return json_encode($this->toArray(), $options);
    }
}
