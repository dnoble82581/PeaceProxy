<?php

namespace App\DTOs\CallEvent;

class CallEventDTO
{
    public function __construct(
        public ?int $id = null,
        public ?int $call_id = null,
        public ?string $type = null,
        public ?array $payload = null,
        public ?int $sequence = null,
        public ?string $occurred_at = null,
        public ?string $created_at = null,
        public ?string $updated_at = null,
    ) {
    }

    /**
     * Create a DTO from an array.
     */
    public static function fromArray(array $data): CallEventDTO
    {
        return new self(
            id: $data['id'] ?? null,
            call_id: $data['call_id'] ?? null,
            type: $data['type'] ?? null,
            payload: $data['payload'] ?? null,
            sequence: $data['sequence'] ?? null,
            occurred_at: $data['occurred_at'] ?? null,
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
            'call_id' => $this->call_id,
            'type' => $this->type,
            'payload' => $this->payload,
            'sequence' => $this->sequence,
            'occurred_at' => $this->occurred_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
