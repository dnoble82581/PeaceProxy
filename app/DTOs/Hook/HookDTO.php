<?php

namespace App\DTOs\Hook;

use App\Enums\General\ConfidenceScore;
use App\Enums\Hook\HookCategories;
use App\Enums\Hook\HookSensitivityLevels;
use Carbon\Carbon;

class HookDTO
{
    public function __construct(
        public ?int $id = null,
        public ?int $tenant_id = null,
        public ?int $subject_id = null,
        public ?int $created_by_id = null,
        public ?int $negotiation_id = null,
        public ?string $title = null,
        public ?string $description = null,
        public ?HookCategories $category = null,
        public ?HookSensitivityLevels $sensitivity_level = null,
        public ?string $source = null,
        public ?ConfidenceScore $confidence_score = null,
        public ?Carbon $created_at = null,
        public ?Carbon $updated_at = null,
        public ?Carbon $deleted_at = null,
    ) {
    }

    public static function fromArray(array $data): HookDTO
    {
        return new self(
            $data['id'] ?? null,
            $data['tenant_id'] ?? null,
            $data['subject_id'] ?? null,
            $data['created_by_id'] ?? null,
            $data['negotiation_id'] ?? null,
            $data['title'] ?? null,
            $data['description'] ?? null,
            isset($data['category'])
                ? ($data['category'] instanceof HookCategories
                    ? $data['category']
                    : HookCategories::from($data['category']))
                : null,
            isset($data['sensitivity_level'])
                ? ($data['sensitivity_level'] instanceof HookSensitivityLevels
                    ? $data['sensitivity_level']
                    : HookSensitivityLevels::from($data['sensitivity_level']))
                : null,
            $data['source'] ?? null,
            isset($data['confidence_score'])
                ? ($data['confidence_score'] instanceof ConfidenceScore
                    ? $data['confidence_score']
                    : ConfidenceScore::fromMixed($data['confidence_score']))
                : null,
            isset($data['created_at']) ? Carbon::parse($data['created_at']) : null,
            isset($data['updated_at']) ? Carbon::parse($data['updated_at']) : null,
            isset($data['deleted_at']) ? Carbon::parse($data['deleted_at']) : null,
        );
    }

    public function toJson($options = 0): string
    {
        return json_encode($this->toArray(), $options);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'tenant_id' => $this->tenant_id,
            'subject_id' => $this->subject_id,
            'created_by_id' => $this->created_by_id,
            'negotiation_id' => $this->negotiation_id,
            'title' => $this->title,
            'description' => $this->description,
            'category' => $this->category?->value,
            'sensitivity_level' => $this->sensitivity_level?->value,
            'source' => $this->source,
            'confidence_score' => $this->confidence_score,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ];
    }
}
