<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

class CreateContactForm extends Form
{
    #[Validate(['required', 'integer'])]
    public $tenant_id = '';

    #[Validate(['required', 'integer'])]
    public $contactable_id = '';

    #[Validate(['required', 'string'])]
    public $contactable_type = '';

    #[Validate(['required', 'string'])]
    public $type = '';

    #[Validate(['nullable', 'numeric', 'min:0', 'max:1'])]
    public $confidence_score = null;

    #[Validate(['boolean'])]
    public $is_primary = false;

    #[Validate(['nullable', 'string'])]
    public $notes = '';

    // For phone numbers
    #[Validate(['nullable', 'string'])]
    public $phone_number = '';

    // For emails
    #[Validate(['nullable', 'email'])]
    public $email = '';

    // For addresses
    #[Validate(['nullable', 'string'])]
    public $street = '';

    #[Validate(['nullable', 'string'])]
    public $city = '';

    #[Validate(['nullable', 'string'])]
    public $state = '';

    #[Validate(['nullable', 'string'])]
    public $zip = '';

    #[Validate(['nullable', 'string'])]
    public $country = '';
}
