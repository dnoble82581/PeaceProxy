<?php

namespace App\DTOs\AssessmentTemplate;

use Carbon\Carbon;

class AssessmentTemplateDTO
{
    public function __construct(
        public ?int $id = null,
        public ?string $name = null,
        public ?int $tenant_id = null,
        public ?string $description = null,
        public ?Carbon $created_at = null,
        public ?Carbon $updated_at = null,
    ) {
    }

    public static function fromArray(array $data): AssessmentTemplateDTO
    {
        return new self(
            $data['id'] ?? null,
            $data['name'] ?? null,
            $data['tenant_id'] ?? null,
            $data['description'] ?? null,
            isset($data['created_at']) ? Carbon::parse($data['created_at']) : null,
            isset($data['updated_at']) ? Carbon::parse($data['updated_at']) : null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'tenant_id' => $this->tenant_id,
            'description' => $this->description,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
