<?php

namespace App\DTOs\MessageReaction;

class MessageReactionDTO
{
    public function __construct(
        public readonly ?int $id = null,
        public readonly ?int $message_id = null,
        public readonly ?int $user_id = null,
        public readonly ?int $tenant_id = null,
        public readonly ?int $negotiation_id = null,
        public readonly ?string $reaction_type = null,
        public readonly ?string $created_at = null,
        public readonly ?string $updated_at = null,
    ) {
    }

    /**
     * Create a DTO from an array.
     */
    public static function fromArray(array $data): MessageReactionDTO
    {
        return new self(
            id: $data['id'] ?? null,
            message_id: $data['message_id'] ?? null,
            user_id: $data['user_id'] ?? null,
            tenant_id: $data['tenant_id'] ?? null,
            negotiation_id: $data['negotiation_id'] ?? null,
            reaction_type: $data['reaction_type'] ?? null,
            created_at: $data['created_at'] ?? null,
            updated_at: $data['updated_at'] ?? null,
        );
    }

    /**
     * Convert the DTO to an array.
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'message_id' => $this->message_id,
            'user_id' => $this->user_id,
            'tenant_id' => $this->tenant_id,
            'negotiation_id' => $this->negotiation_id,
            'reaction_type' => $this->reaction_type,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
