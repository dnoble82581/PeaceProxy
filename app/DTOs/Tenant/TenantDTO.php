<?php

namespace App\DTOs\Tenant;

use Carbon\Carbon;

class TenantDTO
{
    public function __construct(
        public ?int $id = null,
        public ?string $agency_name = null,
        public ?string $subdomain = null,
        public ?string $agency_type = null,
        public ?string $agency_email = null,
        public ?string $agency_phone = null,
        public ?string $agency_website = null,
        public ?string $address_line_1 = null,
        public ?string $address_line_2 = null,
        public ?string $city = null,
        public ?string $state = null,
        public ?string $postal_code = null,
        public ?string $country = null,
        public ?string $agency_identifier = null,
        public ?string $federal_agency_code = null,
        public ?string $timezone = null,
        public ?string $locale = null,
        public ?bool $is_active = null,
        public ?bool $onboarding_complete = null,
        public ?Carbon $trial_ends_at = null,
        public ?Carbon $subscription_ends_at = null,
        public ?string $logo_path = null,
        public ?string $primary_color = null,
        public ?string $secondary_color = null,
        public ?array $settings = null,
        public ?Carbon $created_at = null,
        public ?Carbon $updated_at = null,
    ) {
    }

    public static function fromArray(array $data): TenantDTO
    {
        return new self(
            $data['id'] ?? null,
            $data['agency_name'] ?? null,
            $data['subdomain'] ?? null,
            $data['agency_type'] ?? null,
            $data['agency_email'] ?? null,
            $data['agency_phone'] ?? null,
            $data['agency_website'] ?? null,
            $data['address_line_1'] ?? null,
            $data['address_line_2'] ?? null,
            $data['city'] ?? null,
            $data['state'] ?? null,
            $data['postal_code'] ?? null,
            $data['country'] ?? null,
            $data['agency_identifier'] ?? null,
            $data['federal_agency_code'] ?? null,
            $data['timezone'] ?? null,
            $data['locale'] ?? null,
            $data['is_active'] ?? null,
            $data['onboarding_complete'] ?? null,
            isset($data['trial_ends_at']) ? Carbon::parse($data['trial_ends_at']) : null,
            isset($data['subscription_ends_at']) ? Carbon::parse($data['subscription_ends_at']) : null,
            $data['logo_path'] ?? null,
            $data['primary_color'] ?? null,
            $data['secondary_color'] ?? null,
            $data['settings'] ?? null,
            isset($data['created_at']) ? Carbon::parse($data['created_at']) : null,
            isset($data['updated_at']) ? Carbon::parse($data['updated_at']) : null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'agency_name' => $this->agency_name,
            'subdomain' => $this->subdomain,
            'agency_type' => $this->agency_type,
            'agency_email' => $this->agency_email,
            'agency_phone' => $this->agency_phone,
            'agency_website' => $this->agency_website,
            'address_line_1' => $this->address_line_1,
            'address_line_2' => $this->address_line_2,
            'city' => $this->city,
            'state' => $this->state,
            'postal_code' => $this->postal_code,
            'country' => $this->country,
            'agency_identifier' => $this->agency_identifier,
            'federal_agency_code' => $this->federal_agency_code,
            'timezone' => $this->timezone,
            'locale' => $this->locale,
            'is_active' => $this->is_active,
            'onboarding_complete' => $this->onboarding_complete,
            'trial_ends_at' => $this->trial_ends_at,
            'subscription_ends_at' => $this->subscription_ends_at,
            'logo_path' => $this->logo_path,
            'primary_color' => $this->primary_color,
            'secondary_color' => $this->secondary_color,
            'settings' => $this->settings,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    public function toJson($options = 0): string
    {
        return json_encode($this->toArray(), $options);
    }
}
