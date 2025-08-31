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
        public ?string $billing_email = null,
        public ?string $billing_phone = null,
        public ?string $tax_id = null,
        public ?string $address_line1 = null,
        public ?string $address_line2 = null,
        public ?string $address_city = null,
        public ?string $address_state = null,
        public ?string $address_postal = null,
        public ?string $address_country = null,
        public ?string $agency_identifier = null,
        public ?string $federal_agency_code = null,
        public ?string $timezone = null,
        public ?string $locale = null,
        public ?bool $is_active = null,
        public ?bool $onboarding_complete = null,
        public ?string $stripe_id = null,
        public ?string $pm_type = null,
        public ?string $pm_last_four = null,
        public ?Carbon $trial_ends_at = null,
        public ?int $billing_owner_id = null,
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
            $data['billing_email'] ?? null,
            $data['billing_phone'] ?? null,
            $data['tax_id'] ?? null,
            $data['address_line1'] ?? null,
            $data['address_line2'] ?? null,
            $data['address_city'] ?? null,
            $data['address_state'] ?? null,
            $data['address_postal'] ?? null,
            $data['address_country'] ?? null,
            $data['agency_identifier'] ?? null,
            $data['federal_agency_code'] ?? null,
            $data['timezone'] ?? null,
            $data['locale'] ?? null,
            $data['is_active'] ?? null,
            $data['onboarding_complete'] ?? null,
            $data['stripe_id'] ?? null,
            $data['pm_type'] ?? null,
            $data['pm_last_four'] ?? null,
            isset($data['trial_ends_at']) ? Carbon::parse($data['trial_ends_at']) : null,
            $data['billing_owner_id'] ?? null,
            $data['logo_path'] ?? null,
            $data['primary_color'] ?? null,
            $data['secondary_color'] ?? null,
            $data['settings'] ?? null,
            isset($data['created_at']) ? Carbon::parse($data['created_at']) : null,
            isset($data['updated_at']) ? Carbon::parse($data['updated_at']) : null,
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
            'agency_name' => $this->agency_name,
            'subdomain' => $this->subdomain,
            'agency_type' => $this->agency_type,
            'agency_email' => $this->agency_email,
            'agency_phone' => $this->agency_phone,
            'agency_website' => $this->agency_website,
            'billing_email' => $this->billing_email,
            'billing_phone' => $this->billing_phone,
            'tax_id' => $this->tax_id,
            'address_line1' => $this->address_line1,
            'address_line2' => $this->address_line2,
            'address_city' => $this->address_city,
            'address_state' => $this->address_state,
            'address_postal' => $this->address_postal,
            'address_country' => $this->address_country,
            'agency_identifier' => $this->agency_identifier,
            'federal_agency_code' => $this->federal_agency_code,
            'timezone' => $this->timezone,
            'locale' => $this->locale,
            'is_active' => $this->is_active,
            'onboarding_complete' => $this->onboarding_complete,
            'stripe_id' => $this->stripe_id,
            'pm_type' => $this->pm_type,
            'pm_last_four' => $this->pm_last_four,
            'trial_ends_at' => $this->trial_ends_at,
            'billing_owner_id' => $this->billing_owner_id,
            'logo_path' => $this->logo_path,
            'primary_color' => $this->primary_color,
            'secondary_color' => $this->secondary_color,
            'settings' => $this->settings,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
