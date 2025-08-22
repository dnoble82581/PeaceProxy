<?php

namespace App\DTOs\ConversationUser;

class ConversationUserDTO
{
    public function __construct(
        public readonly ?int $id = null,
        public readonly ?int $conversation_id = null,
        public readonly ?int $user_id = null,
        public readonly ?string $joined_at = null,
        public readonly ?string $left_at = null,
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
            'conversation_id' => $this->conversation_id,
            'user_id' => $this->user_id,
            'joined_at' => $this->joined_at,
            'left_at' => $this->left_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    /**
     * Create a DTO from an array.
     */
    public static function fromArray(array $data): ConversationUserDTO
    {
        return new self(
            id: $data['id'] ?? null,
            conversation_id: $data['conversation_id'] ?? null,
            user_id: $data['user_id'] ?? null,
            joined_at: $data['joined_at'] ?? null,
            left_at: $data['left_at'] ?? null,
            created_at: $data['created_at'] ?? null,
            updated_at: $data['updated_at'] ?? null,
        );
    }
}
