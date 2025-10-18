<?php

namespace App\Livewire\Forms;

use App\Models\Tenant;
use App\Services\Tenant\UpdateTenantService;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Validate;
use Livewire\Form;

class UpdateTenantForm extends Form
{
    public ?Tenant $tenant = null;

    #[Locked]
    public ?int $tenantId = null;

    // Core Identification
    #[Validate(['required', 'string', 'min:3', 'max:255'])]
    public string $agency_name = '';

    #[Validate(['required', 'string'])]
    public string $subdomain = '';

    #[Validate(['required', 'string'])]
    public string $agency_type = '';

    // Contact Info
    #[Validate(['required', 'email'])]
    public string $agency_email = '';

    #[Validate(['nullable', 'string', 'max:20'])]
    public ?string $agency_phone = '';

    #[Validate(['nullable', 'url', 'max:255'])]
    public ?string $agency_website = '';

    // Billing Info
    #[Validate(['nullable', 'email', 'max:255'])]
    public ?string $billing_email = '';

    #[Validate(['nullable', 'string', 'max:20'])]
    public ?string $billing_phone = '';

    #[Validate(['nullable', 'string', 'max:255'])]
    public ?string $tax_id = '';

    // Address
    #[Validate(['nullable', 'string', 'max:255'])]
    public ?string $address_line1 = '';

    #[Validate(['nullable'])]
    public ?string $primary_color = null;

    #[Validate(['nullable'])]
    public ?string $secondary_color = null;

    #[Validate(['nullable', 'string', 'max:255'])]
    public ?string $address_line2 = '';

    #[Validate(['nullable', 'string', 'max:255'])]
    public ?string $address_city = '';

    #[Validate(['nullable', 'string', 'max:255'])]
    public ?string $address_state = '';

    #[Validate(['nullable', 'string', 'max:255'])]
    public ?string $address_postal = '';

    #[Validate(['nullable', 'string', 'max:2'])]
    public string $address_country = 'US';

    // Agency Identifiers
    #[Validate(['nullable', 'string', 'max:255'])]
    public ?string $agency_identifier = '';

    #[Validate(['nullable', 'string', 'max:255'])]
    public ?string $federal_agency_code = '';

    // Timezone and Locale
    #[Validate(['nullable', 'string', 'max:255'])]
    public string $timezone = 'America/Chicago';

    #[Validate(['nullable', 'string', 'max:20'])]
    public string $locale = 'en';

    // Billing Owner
    #[Validate(['nullable', 'exists:users,id'])]
    public ?int $billing_owner_id = null;

    public function setTenant(Tenant $tenant): void
    {
        $this->tenant = $tenant;
        $this->tenantId = $tenant->id;
        $this->fill($tenant);
        // Ensure colors hydrate if present on model
        $this->primary_color = $tenant->primary_color;
        $this->secondary_color = $tenant->secondary_color;
    }

    /**
     * @return array<string, array<int, Rule|string>>
     */
    public function rules(): array
    {
        $tenantId = $this->tenantId ?? $this->tenant?->id ?? 0;

        return [
            'agency_name' => ['required', 'string', 'min:3', 'max:255'],
            'subdomain' => [
                'required', 'string',
                Rule::unique('tenants', 'subdomain')->ignore($tenantId),
            ],
            'agency_type' => ['required', 'string'],
            'agency_email' => [
                'required', 'email',
                Rule::unique('tenants', 'agency_email')->ignore($tenantId),
            ],
            'agency_phone' => ['nullable', 'string', 'max:20'],
            'agency_website' => ['nullable', 'url', 'max:255'],
            'billing_email' => ['nullable', 'email', 'max:255'],
            'billing_phone' => ['nullable', 'string', 'max:20'],
            'tax_id' => ['nullable', 'string', 'max:255'],
            'address_line1' => ['nullable', 'string', 'max:255'],
            'address_line2' => ['nullable', 'string', 'max:255'],
            'address_city' => ['nullable', 'string', 'max:255'],
            'address_state' => ['nullable', 'string', 'max:255'],
            'address_postal' => ['nullable', 'string', 'max:255'],
            'address_country' => ['nullable', 'string', 'max:2'],
            'agency_identifier' => ['nullable', 'string', 'max:255'],
            'federal_agency_code' => ['nullable', 'string', 'max:255'],
            'timezone' => ['nullable', 'string', 'max:255'],
            'locale' => ['nullable', 'string', 'max:20'],
            'billing_owner_id' => ['nullable', 'exists:users,id'],
            'primary_color' => ['nullable', 'string'],
            'secondary_color' => ['nullable', 'string'],
        ];
    }

    /**
     * @return array<string, int|string|null>
     */
    public function payload(): array
    {
        return [
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
            'billing_owner_id' => $this->billing_owner_id,
            'primary_color' => $this->primary_color,
            'secondary_color' => $this->secondary_color,
        ];
    }

    public function update(): Tenant
    {
        $this->validate();

        /** @var UpdateTenantService $service */
        $service = app(UpdateTenantService::class);

        return $service->updateTenant($this->tenant ?? new Tenant(), $this->payload());
    }
}
