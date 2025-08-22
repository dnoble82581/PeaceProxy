<?php

namespace App\DTOs\Warrant;

use App\Enums\Warrant\BondType;
use App\Enums\Warrant\WarrantStatus;
use App\Enums\Warrant\WarrantType;
use Carbon\Carbon;

class WarrantDTO
{
    public function __construct(
        public ?int $id = null,
        public ?int $tenant_id = null,
        public ?int $subject_id = null,
        public ?WarrantType $type = null,
        public ?WarrantStatus $status = null,
        public ?string $jurisdiction = null,
        public ?string $court_name = null,
        public ?string $offense_description = null,
        public ?string $status_code = null,
        public ?Carbon $issued_at = null,
        public ?Carbon $expires_at = null,
        public ?float $bond_amount = null,
        public ?BondType $bond_type = null,
        public ?Carbon $created_at = null,
        public ?Carbon $updated_at = null,
    ) {
    }

    public static function fromArray(array $data): WarrantDTO
    {
        return new self(
            $data['id'] ?? null,
            $data['tenant_id'] ?? null,
            $data['subject_id'] ?? null,
            isset($data['type']) ? WarrantType::from($data['type']) : null,
            isset($data['status']) ? WarrantStatus::from($data['status']) : null,
            $data['jurisdiction'] ?? null,
            $data['court_name'] ?? null,
            $data['offense_description'] ?? null,
            $data['status_code'] ?? null,
            isset($data['issued_at']) ? Carbon::parse($data['issued_at']) : null,
            isset($data['expires_at']) ? Carbon::parse($data['expires_at']) : null,
            $data['bond_amount'] ?? null,
            isset($data['bond_type']) ? BondType::from($data['bond_type']) : null,
            isset($data['created_at']) ? Carbon::parse($data['created_at']) : null,
            isset($data['updated_at']) ? Carbon::parse($data['updated_at']) : null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'tenant_id' => $this->tenant_id,
            'subject_id' => $this->subject_id,
            'type' => $this->type?->value,
            'status' => $this->status?->value,
            'jurisdiction' => $this->jurisdiction,
            'court_name' => $this->court_name,
            'offense_description' => $this->offense_description,
            'status_code' => $this->status_code,
            'issued_at' => $this->issued_at,
            'expires_at' => $this->expires_at,
            'bond_amount' => $this->bond_amount,
            'bond_type' => $this->bond_type?->value,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    public function toJson($options = 0): string
    {
        return json_encode($this->toArray(), $options);
    }
}
