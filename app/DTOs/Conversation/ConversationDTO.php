<?php

namespace App\DTOs\Conversation;

class ConversationDTO
{
    public function __construct(
        public readonly ?int $id = null,
        public readonly ?int $tenant_id = null,
        public readonly ?int $created_by = null,
        public readonly ?string $name = null,
        public readonly ?string $type = null,
        public readonly ?bool $is_active = true,
        public readonly ?string $created_at = null,
        public readonly ?string $updated_at = null,
    ) {
    }

    /**
     * Convert the DTO to an array.
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'tenant_id' => $this->tenant_id,
            'created_by' => $this->created_by,
            'name' => $this->name,
            'type' => $this->type,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    /**
     * Create a DTO from an array.
     */
    public static function fromArray(array $data): ConversationDTO
    {
        return new self(
            id: $data['id'] ?? null,
            tenant_id: $data['tenant_id'] ?? null,
            created_by: $data['created_by'] ?? null,
            name: $data['name'] ?? null,
            type: $data['type'] ?? null,
            is_active: $data['is_active'] ?? true,
            created_at: $data['created_at'] ?? null,
            updated_at: $data['updated_at'] ?? null,
        );
    }
}
