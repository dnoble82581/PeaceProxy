<?php

namespace App\DTOs\Message;

class MessageDTO
{
    public function __construct(
        public readonly ?int $id = null,
        public readonly ?int $conversation_id = null,
        public readonly ?int $user_id = null,
        public readonly ?int $tenant_id = null,
        public readonly ?int $negotiation_id = null,
        public readonly ?string $content = null,
        public readonly ?int $whisper_to = null,
        public readonly ?bool $is_whisper = false,
        public readonly ?string $created_at = null,
        public readonly ?string $updated_at = null,
    ) {
    }

    /**
     * Create a DTO from an array.
     */
    public static function fromArray(array $data): MessageDTO
    {
        return new self(
            id: $data['id'] ?? null,
            conversation_id: $data['conversation_id'] ?? null,
            user_id: $data['user_id'] ?? null,
            tenant_id: $data['tenant_id'] ?? null,
            negotiation_id: $data['negotiation_id'] ?? null,
            content: $data['content'] ?? null,
            whisper_to: $data['whisper_to'] ?? null,
            is_whisper: $data['is_whisper'] ?? false,
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
            'conversation_id' => $this->conversation_id,
            'user_id' => $this->user_id,
            'tenant_id' => $this->tenant_id,
            'negotiation_id' => $this->negotiation_id,
            'content' => $this->content,
            'whisper_to' => $this->whisper_to,
            'is_whisper' => $this->is_whisper,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
