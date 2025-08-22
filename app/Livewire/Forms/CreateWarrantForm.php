<?php

namespace App\Livewire\Forms;

use App\Enums\Warrant\WarrantStatus;
use App\Enums\Warrant\WarrantType;
use Livewire\Attributes\Validate;
use Livewire\Form;

class CreateWarrantForm extends Form
{
    #[Validate(['required', 'integer'])]
    public $tenant_id = '';

    #[Validate(['required', 'integer'])]
    public $subject_id = '';

    #[Validate(['required', 'string'])]
    public $type = WarrantType::unknown->value;

    #[Validate(['required', 'string'])]
    public $status = WarrantStatus::active->value;

    #[Validate(['nullable', 'string'])]
    public $jurisdiction = '';

    #[Validate(['nullable', 'string'])]
    public $court_name = '';

    #[Validate(['nullable', 'string'])]
    public $offense_description = '';

    #[Validate(['nullable', 'string'])]
    public $status_code = '';

    #[Validate(['nullable', 'date'])]
    public $issued_at = null;

    #[Validate(['nullable', 'date'])]
    public $expires_at = null;

    #[Validate(['nullable', 'numeric'])]
    public $bond_amount = 0.00;

    #[Validate(['nullable', 'string'])]
    public $bond_type = null;
}
