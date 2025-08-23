<?php

namespace App\DTOs\Pin;

use Carbon\Carbon;

class PinDTO
{
    public function __construct(
        public ?int $id = null,
        public int $tenant_id,
        public int $user_id,
        public string $pinnable_type,
        public int $pinnable_id,
        public ?Carbon $created_at = null,
        public ?Carbon $updated_at = null,
    ) {
    }

    public static function fromArray(array $data): PinDTO
    {
        return new self(
            $data['id'] ?? null,
            $data['tenant_id'],
            $data['user_id'],
            $data['pinnable_type'],
            $data['pinnable_id'],
            isset($data['created_at']) ? Carbon::parse($data['created_at']) : null,
            isset($data['updated_at']) ? Carbon::parse($data['updated_at']) : null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'tenant_id' => $this->tenant_id,
            'user_id' => $this->user_id,
            'pinnable_type' => $this->pinnable_type,
            'pinnable_id' => $this->pinnable_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
