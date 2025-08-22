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

    // Address
    #[Validate(['nullable'])]
    public $address_line_1 = '';

    #[Validate(['nullable'])]
    public $address_line_2 = '';

    #[Validate(['nullable'])]
    public $city = '';

    #[Validate(['nullable', 'size:2'])]
    public $state = '';

    #[Validate(['nullable'])]
    public $postal_code = '';

    #[Validate(['nullable'])]
    public $country = 'US';

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

    public function toArray()
    {
        return [
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
        ];
    }
}
