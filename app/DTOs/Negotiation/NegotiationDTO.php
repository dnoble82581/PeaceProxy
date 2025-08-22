<?php

namespace App\DTOs\Negotiation;

use App\Enums\Negotiation\NegotiationStatuses;
use App\Enums\Negotiation\NegotiationTypes;
use Carbon\Carbon;

class NegotiationDTO
{
    public function __construct(
        public ?int $id = null,
        public ?int $tenant_id = null,
        public ?string $title = null,
        public ?string $description = null,
        public ?NegotiationTypes $type = null,
        public ?NegotiationStatuses $status = null,
        public ?array $tags = null,
        public ?Carbon $started_at = null,
        public ?Carbon $ended_at = null,
        public ?Carbon $created_at = null,
        public ?Carbon $updated_at = null,
    ) {
    }

    public static function fromArray(array $data): NegotiationDTO
    {
        return new self(
            $data['id'] ?? null,
            $data['tenant_id'] ?? null,
            $data['title'] ?? null,
            $data['description'] ?? null,
            isset($data['type']) ? NegotiationTypes::from($data['type']) : null,
            isset($data['status']) ? NegotiationStatuses::from($data['status']) : null,
            $data['tags'] ?? null,
            isset($data['started_at']) ? Carbon::parse($data['started_at']) : null,
            isset($data['ended_at']) ? Carbon::parse($data['ended_at']) : null,
            isset($data['created_at']) ? Carbon::parse($data['created_at']) : null,
            isset($data['updated_at']) ? Carbon::parse($data['updated_at']) : null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'tenant_id' => $this->tenant_id,
            'title' => $this->title,
            'description' => $this->description,
            'type' => $this->type?->value,
            'status' => $this->status?->value,
            'tags' => $this->tags,
            'started_at' => $this->started_at,
            'ended_at' => $this->ended_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    public function toJson($options = 0): string
    {
        return json_encode($this->toArray(), $options);
    }
}
