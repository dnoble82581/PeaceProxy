<?php

namespace App\DTOs\Address;

use Carbon\Carbon;

class AddressDTO
{
    public function __construct(
        public ?int $tenant_id,
        public ?int $contact_id,
        public ?string $address_1 = null,
        public ?string $address_2 = null,
        public ?string $city = null,
        public ?string $state = null,
        public ?string $postal_code = null,
        public ?Carbon $created_at = null,
        public ?Carbon $updated_at = null,
    ) {
    }

    public static function fromArray(array $data): AddressDTO
    {
        return new self(
            $data['tenant_id'] ?? null,
            $data['contact_id'] ?? null,
            $data['address_1'] ?? null,
            $data['address_2'] ?? null,
            $data['city'] ?? null,
            $data['state'] ?? null,
            $data['postal_code'] ?? null,
            isset($data['created_at']) ? Carbon::parse($data['created_at']) : null,
            isset($data['updated_at']) ? Carbon::parse($data['updated_at']) : null,
        );
    }

    public function toArray(): array
    {
        return [
            'tenant_id' => $this->tenant_id,
            'contact_id' => $this->contact_id,
            'address_1' => $this->address_1,
            'address_2' => $this->address_2,
            'city' => $this->city,
            'state' => $this->state,
            'postal_code' => $this->postal_code,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
