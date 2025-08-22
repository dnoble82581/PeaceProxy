<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

class CreateContactPointForm extends Form
{
    #[Validate(['required', 'integer'])]
    public $tenant_id = '';

    #[Validate(['required', 'integer'])]
    public $subject_id = '';

    #[Validate(['required', 'string', 'in:email,phone,address'])]
    public $kind = 'email';

    #[Validate(['nullable', 'string'])]
    public $label = '';

    #[Validate(['boolean'])]
    public $is_primary = false;

    #[Validate(['boolean'])]
    public $is_verified = false;

    #[Validate(['nullable', 'integer'])]
    public $priority = 0;

    // Email fields
    #[Validate(['required_if:kind,email', 'nullable', 'email'])]
    public $email = '';

    // Phone fields
    #[Validate(['required_if:kind,phone', 'nullable', 'string', 'max:20'])]
    public $e164 = '';

    #[Validate(['nullable', 'string', 'max:10'])]
    public $ext = '';

    #[Validate(['nullable', 'string', 'size:2'])]
    public $phone_country_iso = '';

    // Address fields
    #[Validate(['required_if:kind,address', 'nullable', 'string'])]
    public $address1 = '';

    #[Validate(['nullable', 'string'])]
    public $address2 = '';

    #[Validate(['nullable', 'string'])]
    public $city = '';

    #[Validate(['nullable', 'string'])]
    public $region = '';

    #[Validate(['nullable', 'string', 'max:5'])]
    public $postal_code = '';

    #[Validate(['required_if:kind,address', 'nullable', 'string', 'size:2'])]
    public $address_country_iso = '';

    #[Validate(['nullable', 'numeric'])]
    public $latitude = null;

    #[Validate(['nullable', 'numeric'])]
    public $longitude = null;
}
