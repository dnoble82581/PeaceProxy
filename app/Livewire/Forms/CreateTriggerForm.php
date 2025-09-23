<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

class CreateTriggerForm extends Form
{
    #[Validate(['required', 'integer'])]
    public $tenant_id = '';

    #[Validate(['required', 'integer'])]
    public $subject_id = '';

    #[Validate(['nullable', 'integer'])]
    public $created_by_id = '';

    #[Validate(['nullable', 'integer'])]
    public $negotiation_id = '';

    #[Validate(['required'])]
    public $title = '';

    #[Validate(['nullable'])]
    public $description = '';

    #[Validate(['required'])]
    public $category = '';

    #[Validate(['required'])]
    public $sensitivity_level = '';

    #[Validate(['nullable'])]
    public $source = '';

    #[Validate(['required'])]
    public $confidence_score = '';

    #[Validate(['nullable', 'date'])]
    public $deleted_at = null;
}
