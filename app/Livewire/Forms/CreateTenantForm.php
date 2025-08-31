<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

class CreateTenantForm extends Form
{
    // Core Identification
    #[Validate(['required'])]
    public $agency_name = '';

    #[Validate(['required', 'unique:tenants,subdomain'])]
    public $subdomain = '';

    #[Validate(['required', 'string'])]
    public $agency_type = '';

    // Contact Info
    #[Validate(['nullable', 'email'])]
    public $agency_email = '';

    #[Validate(['nullable'])]
    public $agency_phone = '';

    #[Validate(['nullable', 'url'])]
    public $agency_website = '';

    // Billing Info
    #[Validate(['nullable', 'email'])]
    public $billing_email = '';

    #[Validate(['nullable'])]
    public $billing_phone = '';

    #[Validate(['nullable'])]
    public $tax_id = '';

    // Address
    #[Validate(['nullable'])]
    public $address_line1 = '';

    #[Validate(['nullable'])]
    public $address_line2 = '';

    #[Validate(['nullable'])]
    public $address_city = '';

    #[Validate(['nullable', 'size:2'])]
    public $address_state = '';

    #[Validate(['nullable'])]
    public $address_postal = '';

    #[Validate(['nullable'])]
    public $address_country = 'US';

    // Agency Identifiers
    #[Validate(['nullable'])]
    public $agency_identifier = '';

    #[Validate(['nullable'])]
    public $federal_agency_code = '';

    // Timezone and Locale
    #[Validate(['nullable'])]
    public $timezone = 'America/Chicago';

    #[Validate(['nullable'])]
    public $locale = 'en';

    // Billing Owner
    #[Validate(['nullable', 'exists:users,id'])]
    public $billing_owner_id = null;

    public function toArray()
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
        ];
    }
}
