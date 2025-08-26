<?php

namespace App\DTOs\Trigger;

use App\Enums\General\ConfidenceScore;
use App\Enums\Trigger\TriggerCategories;
use App\Enums\Trigger\TriggerSensitivityLevels;
use Carbon\Carbon;

class TriggerDTO
{
    public function __construct(
        public ?int $tenant_id,
        public int $subject_id,
        public ?int $created_by_id = null,
        public ?int $negotiation_id = null,

        // Core fields
        public string $title,
        public ?string $description = null,
        public ?TriggerCategories $category = null,
        public ?TriggerSensitivityLevels $sensitivity_level = null,
        public ?string $source = null,
        public ?ConfidenceScore $confidence_score = null,
        public ?Carbon $created_at = null,
        public ?Carbon $updated_at = null,
    ) {
    }

    public static function fromArray(array $data): TriggerDTO
    {
        return new self(
            $data['tenant_id'] ?? null,
            $data['subject_id'],
            $data['created_by_id'] ?? null,
            $data['negotiation_id'] ?? null,

            // Core fields
            $data['title'],
            $data['description'] ?? null,
            isset($data['category']) ? TriggerCategories::from($data['category']) : null,
            isset($data['sensitivity_level']) ? TriggerSensitivityLevels::from($data['sensitivity_level']) : null,
            $data['source'] ?? null,
            isset($data['confidence_score'])
                ? ($data['confidence_score'] instanceof ConfidenceScore
                    ? $data['confidence_score']
                    : ConfidenceScore::fromMixed($data['confidence_score']))
                : null,
            isset($data['created_at']) ? Carbon::parse($data['created_at']) : null,
            isset($data['updated_at']) ? Carbon::parse($data['updated_at']) : null,
        );
    }

    public function toArray(): array
    {
        return [
            'tenant_id' => $this->tenant_id,
            'subject_id' => $this->subject_id,
            'created_by_id' => $this->created_by_id,
            'negotiation_id' => $this->negotiation_id,

            // Core fields
            'title' => $this->title,
            'description' => $this->description,
            'category' => $this->category?->value,
            'sensitivity_level' => $this->sensitivity_level?->value,
            'source' => $this->source,
            'confidence_score' => $this->confidence_score,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
