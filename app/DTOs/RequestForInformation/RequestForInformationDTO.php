<?php

namespace App\DTOs\RequestForInformation;

use Carbon\Carbon;

class RequestForInformationDTO
{
    public function __construct(
        public ?int $id = null,
        public ?int $tenant_id = null,
        public ?int $negotiation_id = null,
        public ?int $user_id = null,
        public ?string $title = null,
        public ?string $body = null,
        public ?string $status = null,
        public ?Carbon $due_date = null,
        public ?Carbon $created_at = null,
        public ?Carbon $updated_at = null,
        public ?Carbon $deleted_at = null,
    ) {
    }

    public static function fromArray(array $data): RequestForInformationDTO
    {
        return new self(
            $data['id'] ?? null,
            $data['tenant_id'] ?? null,
            $data['negotiation_id'] ?? null,
            $data['user_id'] ?? null,
            $data['title'] ?? null,
            $data['body'] ?? null,
            $data['status'] ?? null,
            isset($data['due_date']) ? Carbon::parse($data['due_date']) : null,
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
            'negotiation_id' => $this->negotiation_id,
            'user_id' => $this->user_id,
            'title' => $this->title,
            'body' => $this->body,
            'status' => $this->status,
            'due_date' => $this->due_date,
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
