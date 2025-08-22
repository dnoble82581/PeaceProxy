<?php

namespace App\DTOs\Contact;

use Carbon\Carbon;

class ContactDTO
{
    public function __construct(
        public ?int $tenant_id,
        public ?int $contactable_id,
        public ?string $contactable_type,
        public string $type,
        public ?float $confidence_score = null,
        public bool $is_primary = false,
        public ?string $notes = null,
        public ?Carbon $created_at = null,
        public ?Carbon $updated_at = null,
    ) {
    }

    public static function fromArray(array $data): ContactDTO
    {
        return new self(
            $data['tenant_id'] ?? null,
            $data['contactable_id'] ?? null,
            $data['contactable_type'] ?? null,
            $data['type'],
            $data['confidence_score'] ?? null,
            $data['is_primary'] ?? false,
            $data['notes'] ?? null,
            isset($data['created_at']) ? Carbon::parse($data['created_at']) : null,
            isset($data['updated_at']) ? Carbon::parse($data['updated_at']) : null,
        );
    }

    public function toArray(): array
    {
        return [
            'tenant_id' => $this->tenant_id,
            'contactable_id' => $this->contactable_id,
            'contactable_type' => $this->contactable_type,
            'type' => $this->type,
            'confidence_score' => $this->confidence_score,
            'is_primary' => $this->is_primary,
            'notes' => $this->notes,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
