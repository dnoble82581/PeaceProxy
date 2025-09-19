<?php

namespace App\DTOs\DeliveryPlan;

use Carbon\Carbon;

class DeliveryPlanDTO
{
    public function __construct(
        public ?int $id = null,
        public ?int $tenant_id = null,
        public ?int $negotiation_id = null,
        public ?int $created_by = null,
        public ?int $updated_by = null,
        public ?string $title = null,
        public ?string $summary = null,
        public ?string $category = null,
        public ?string $status = null,
        public ?Carbon $scheduled_at = null,
        public ?Carbon $window_starts_at = null,
        public ?Carbon $window_ends_at = null,
        public ?string $location_name = null,
        public ?string $location_address = null,
        public ?array $geo = null,
        public ?array $route = null,
        public ?array $instructions = null,
        public ?array $constraints = null,
        public ?array $contingencies = null,
        public ?array $risk_assessment = null,
        public ?array $signals = null,
        public ?Carbon $created_at = null,
        public ?Carbon $updated_at = null,
        public ?Carbon $deleted_at = null,
    ) {
    }

    public static function fromArray(array $data): DeliveryPlanDTO
    {
        return new self(
            $data['id'] ?? null,
            $data['tenant_id'] ?? null,
            $data['negotiation_id'] ?? null,
            $data['created_by'] ?? null,
            $data['updated_by'] ?? null,
            $data['title'] ?? null,
            $data['summary'] ?? null,
            $data['category'] ?? null,
            $data['status'] ?? null,
            isset($data['scheduled_at']) ? Carbon::parse($data['scheduled_at']) : null,
            isset($data['window_starts_at']) ? Carbon::parse($data['window_starts_at']) : null,
            isset($data['window_ends_at']) ? Carbon::parse($data['window_ends_at']) : null,
            $data['location_name'] ?? null,
            $data['location_address'] ?? null,
            $data['geo'] ?? null,
            $data['route'] ?? null,
            $data['instructions'] ?? null,
            $data['constraints'] ?? null,
            $data['contingencies'] ?? null,
            $data['risk_assessment'] ?? null,
            $data['signals'] ?? null,
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
            'negotiation_id' => $this->negotiation_id,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'title' => $this->title,
            'summary' => $this->summary,
            'category' => $this->category,
            'status' => $this->status,
            'scheduled_at' => $this->scheduled_at,
            'window_starts_at' => $this->window_starts_at,
            'window_ends_at' => $this->window_ends_at,
            'location_name' => $this->location_name,
            'location_address' => $this->location_address,
            'geo' => $this->geo,
            'route' => $this->route,
            'instructions' => $this->instructions,
            'constraints' => $this->constraints,
            'contingencies' => $this->contingencies,
            'risk_assessment' => $this->risk_assessment,
            'signals' => $this->signals,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ];
    }
}
