<?php

namespace App\DTOs\Email;

use Carbon\Carbon;

class EmailDTO
{
    public function __construct(
        public ?int $tenant_id,
        public ?int $contact_id,
        public string $email,
        public ?Carbon $created_at = null,
        public ?Carbon $updated_at = null,
    ) {
    }

    public static function fromArray(array $data): EmailDTO
    {
        return new self(
            $data['tenant_id'] ?? null,
            $data['contact_id'] ?? null,
            $data['email'],
            isset($data['created_at']) ? Carbon::parse($data['created_at']) : null,
            isset($data['updated_at']) ? Carbon::parse($data['updated_at']) : null,
        );
    }

    public function toArray(): array
    {
        return [
            'tenant_id' => $this->tenant_id,
            'contact_id' => $this->contact_id,
            'email' => $this->email,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
