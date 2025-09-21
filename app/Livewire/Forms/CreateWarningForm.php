<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

class CreateWarningForm extends Form
{
    #[Validate(['required', 'integer'])]
    public $subject_id = '';

    #[Validate(['required', 'integer'])]
    public $tenant_id = '';

    #[Validate(['required', 'integer'])]
    public $created_by_id = '';

    #[Validate(['required', 'string', 'in:low,medium,high'])]
    public $risk_level = 'low';

    #[Validate(['required', 'string', 'in:medical,substance_abuse,weapons,mental_health,violence,suicidal,self_harm,allergies,medications,other'])]
    public $warning_type = '';

    #[Validate(['required', 'string'])]
    public $warning = '';

    #[Validate(['nullable', 'date'])]
    public $created_at = null;

    #[Validate(['nullable', 'date'])]
    public $updated_at = null;
}
