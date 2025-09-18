<?php

namespace App\DTOs\MessageDocument;

class MessageDocumentDTO
{
    public function __construct(
        public readonly ?int $id = null,
        public readonly ?int $message_id = null,
        public readonly ?int $document_id = null,
        public readonly ?int $tenant_id = null,
        public readonly ?int $negotiation_id = null,
        public readonly ?int $uploaded_by_id = null,
        public readonly ?string $created_at = null,
        public readonly ?string $updated_at = null,
    ) {
    }

    /**
     * Create a DTO from an array.
     */
    public static function fromArray(array $data): MessageDocumentDTO
    {
        return new self(
            id: $data['id'] ?? null,
            message_id: $data['message_id'] ?? null,
            document_id: $data['document_id'] ?? null,
            tenant_id: $data['tenant_id'] ?? null,
            negotiation_id: $data['negotiation_id'] ?? null,
            uploaded_by_id: $data['uploaded_by_id'] ?? null,
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
            'document_id' => $this->document_id,
            'tenant_id' => $this->tenant_id,
            'negotiation_id' => $this->negotiation_id,
            'uploaded_by_id' => $this->uploaded_by_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
