<?php

namespace App\DTOs\MoodLog;

class MoodLogDTO
{
    public function __construct(
        public ?int $id = null,
        public ?int $tenant_id = null,
        public ?int $subject_id = null,
        public ?int $logged_by_id = null,
        public ?int $negotiation_id = null,
        public ?string $mood_level = null,
        public ?string $context = null,
        public ?array $meta_data = null,
        public ?string $created_at = null,
        public ?string $updated_at = null,
    ) {
    }

    /**
     * Create a DTO from an array.
     */
    public static function fromArray(array $data): MoodLogDTO
    {
        return new self(
            id: $data['id'] ?? null,
            tenant_id: $data['tenant_id'] ?? null,
            subject_id: $data['subject_id'] ?? null,
            logged_by_id: $data['logged_by_id'] ?? null,
            negotiation_id: $data['negotiation_id'] ?? null,
            mood_level: $data['mood_level'] ?? null,
            context: $data['context'] ?? null,
            meta_data: $data['meta_data'] ?? null,
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
            'tenant_id' => $this->tenant_id,
            'subject_id' => $this->subject_id,
            'logged_by_id' => $this->logged_by_id,
            'negotiation_id' => $this->negotiation_id,
            'mood_level' => $this->mood_level,
            'context' => $this->context,
            'meta_data' => $this->meta_data,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
