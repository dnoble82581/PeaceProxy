<?php

namespace App\DTOs\Hostage;

class HostageDTO
{
    public function __construct(
        public ?int $id = null,
        public ?int $tenant_id = null,
        public ?int $negotiation_id = null,
        public ?string $name = null,
        public ?string $age = null,
        public ?string $gender = null,
        public ?string $relation_to_subject = null,
        public ?string $risk_level = null,
        public ?string $location = null,
        public ?string $phone = null,
        public ?string $email = null,
        public ?string $address = null,
        public ?string $status = null,
        public ?string $injury_status = null,
        public ?array $risk_factors = null,
        public ?string $notes = null,
        public ?string $last_seen_at = null,
        public ?string $freed_at = null,
        public ?string $deceased_at = null,
        public ?bool $is_primary_hostage = false,
        public ?int $created_by = null,
        public ?string $created_at = null,
        public ?string $updated_at = null,
    ) {
    }

    /**
     * Convert the DTO to an array.
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'tenant_id' => $this->tenant_id,
            'negotiation_id' => $this->negotiation_id,
            'name' => $this->name,
            'age' => $this->age,
            'gender' => $this->gender,
            'relation_to_subject' => $this->relation_to_subject,
            'risk_level' => $this->risk_level,
            'location' => $this->location,
            'phone' => $this->phone,
            'email' => $this->email,
            'address' => $this->address,
            'status' => $this->status,
            'injury_status' => $this->injury_status,
            'risk_factors' => $this->risk_factors,
            'notes' => $this->notes,
            'last_seen_at' => $this->last_seen_at,
            'freed_at' => $this->freed_at,
            'deceased_at' => $this->deceased_at,
            'is_primary_hostage' => $this->is_primary_hostage,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    /**
     * Create a DTO from an array.
     */
    public static function fromArray(array $data): HostageDTO
    {
        return new self(
            id: $data['id'] ?? null,
            tenant_id: $data['tenant_id'] ?? null,
            negotiation_id: $data['negotiation_id'] ?? null,
            name: $data['name'] ?? null,
            age: $data['age'] ?? null,
            gender: $data['gender'] ?? null,
            relation_to_subject: $data['relation_to_subject'] ?? null,
            risk_level: $data['risk_level'] ?? null,
            location: $data['location'] ?? null,
            phone: $data['phone'] ?? null,
            email: $data['email'] ?? null,
            address: $data['address'] ?? null,
            status: $data['status'] ?? null,
            injury_status: $data['injury_status'] ?? null,
            risk_factors: is_string($data['risk_factors'] ?? null) ? json_decode($data['risk_factors'], true) : ($data['risk_factors'] ?? null),
            notes: $data['notes'] ?? null,
            last_seen_at: $data['last_seen_at'] ?? null,
            freed_at: $data['freed_at'] ?? null,
            deceased_at: $data['deceased_at'] ?? null,
            is_primary_hostage: $data['is_primary_hostage'] ?? false,
            created_by: $data['created_by'] ?? null,
            created_at: $data['created_at'] ?? null,
            updated_at: $data['updated_at'] ?? null,
        );
    }
}
