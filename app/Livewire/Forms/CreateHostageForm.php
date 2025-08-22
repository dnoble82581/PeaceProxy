<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

class CreateHostageForm extends Form
{
    #[Validate(['required', 'integer'])]
    public $tenant_id = '';

    #[Validate(['required', 'integer'])]
    public $negotiation_id = '';

    #[Validate(['required'])]
    public $name = '';

    #[Validate(['nullable'])]
    public $age = '';

    #[Validate(['nullable'])]
    public $gender = '';

    #[Validate(['nullable'])]
    public $relation_to_subject = '';

    #[Validate(['nullable'])]
    public $risk_level = '';

    #[Validate(['nullable'])]
    public $location = '';

    #[Validate(['nullable'])]
    public $status = '';

    #[Validate(['nullable', 'date'])]
    public $last_seen_at = null;

    #[Validate(['nullable', 'date'])]
    public $freed_at = null;

    #[Validate(['nullable', 'date'])]
    public $deceased_at = null;

    #[Validate(['boolean'])]
    public $is_primary_hostage = false;

    #[Validate(['nullable'])]
    public $injury_status = null;

    #[Validate(['nullable'])]
    public $risk_factors = '';

    #[Validate(['required', 'integer'])]
    public $created_by = '';
}
