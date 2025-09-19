<?php

namespace App\DTOs\DeliveryPlannables;

use Carbon\Carbon;

class DeliveryPlannablesDTO
{
    public function __construct(
        public ?int $id = null,
        public ?int $delivery_plan_id = null,
        public ?string $planable_type = null,
        public ?int $planable_id = null,
        public ?string $role = null,
        public ?string $notes = null,
        public ?Carbon $created_at = null,
        public ?Carbon $updated_at = null,
    ) {
    }

    public static function fromArray(array $data): DeliveryPlannablesDTO
    {
        return new self(
            $data['id'] ?? null,
            $data['delivery_plan_id'] ?? null,
            $data['planable_type'] ?? null,
            $data['planable_id'] ?? null,
            $data['role'] ?? null,
            $data['notes'] ?? null,
            isset($data['created_at']) ? Carbon::parse($data['created_at']) : null,
            isset($data['updated_at']) ? Carbon::parse($data['updated_at']) : null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'delivery_plan_id' => $this->delivery_plan_id,
            'planable_type' => $this->planable_type,
            'planable_id' => $this->planable_id,
            'role' => $this->role,
            'notes' => $this->notes,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    public function toJson($options = 0): string
    {
        return json_encode($this->toArray(), $options);
    }
}
