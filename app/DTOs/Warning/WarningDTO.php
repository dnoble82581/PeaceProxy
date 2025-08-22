<?php

namespace App\DTOs\Warning;

use App\Enums\General\RiskLevels;
use App\Enums\Warning\WarningTypes;
use Carbon\Carbon;

class WarningDTO
{
    public function __construct(
        public ?int $id = null,
        public ?int $subject_id = null,
        public ?int $tenant_id = null,
        public ?int $created_by_id = null,
        public RiskLevels|string|null $risk_level = 'low',
        public WarningTypes|string|null $warning_type = null,
        public ?string $warning = null,
        public ?Carbon $created_at = null,
        public ?Carbon $updated_at = null,
    ) {
        // Convert string values to enum instances if needed
        if (is_string($this->risk_level) && $this->risk_level !== null) {
            $this->risk_level = RiskLevels::from($this->risk_level);
        }

        if (is_string($this->warning_type) && $this->warning_type !== null) {
            $this->warning_type = WarningTypes::from($this->warning_type);
        }
    }

    /**
     * Create a DTO from an array.
     */
    public static function fromArray(array $data): WarningDTO
    {
        // The constructor will handle converting string values to enum instances
        return new self(
            $data['id'] ?? null,
            $data['subject_id'] ?? null,
            $data['tenant_id'] ?? null,
            $data['created_by_id'] ?? null,
            $data['risk_level'] ?? 'low',
            $data['warning_type'] ?? null,
            $data['warning'] ?? null,
            isset($data['created_at']) ? Carbon::parse($data['created_at']) : null,
            isset($data['updated_at']) ? Carbon::parse($data['updated_at']) : null,
        );
    }

    /**
     * Convert the DTO to an array.
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'subject_id' => $this->subject_id,
            'tenant_id' => $this->tenant_id,
            'created_by_id' => $this->created_by_id,
            'risk_level' => $this->risk_level instanceof RiskLevels ? $this->risk_level->value : $this->risk_level,
            'warning_type' => $this->warning_type instanceof WarningTypes ? $this->warning_type->value : $this->warning_type,
            'warning' => $this->warning,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
