<?php

namespace App\DTOs\Note;

use Carbon\Carbon;

class NoteDTO
{
    public function __construct(
        public ?int $id = null,
        public ?int $negotiation_id = null,
        public int $tenant_id,
        public int $author_id,
        public ?string $title = null,
        public string $body,
        public bool $is_private = false,
        public bool $pinned = false,
        public ?array $tags = null,
        public ?Carbon $created_at = null,
        public ?Carbon $updated_at = null,
    ) {
    }

    public static function fromArray(array $data): NoteDTO
    {
        return new self(
            $data['id'] ?? null,
            $data['negotiation_id'] ?? null,
            $data['tenant_id'],
            $data['author_id'],
            $data['title'] ?? null,
            $data['body'],
            $data['is_private'] ?? false,
            $data['pinned'] ?? false,
            $data['tags'] ?? null,
            isset($data['created_at']) ? Carbon::parse($data['created_at']) : null,
            isset($data['updated_at']) ? Carbon::parse($data['updated_at']) : null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'negotiation_id' => $this->negotiation_id,
            'tenant_id' => $this->tenant_id,
            'author_id' => $this->author_id,
            'title' => $this->title,
            'body' => $this->body,
            'is_private' => $this->is_private,
            'pinned' => $this->pinned,
            'tags' => $this->tags,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
