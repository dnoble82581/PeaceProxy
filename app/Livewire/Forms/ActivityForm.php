<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

class ActivityForm extends Form
{
    #[Validate(['required', 'integer'])]
    public $tenant_id = '';

    #[Validate(['required', 'integer'])]
    public $negotiation_id = '';

    #[Validate(['required', 'integer'])]
    public $user_id = '';

    #[Validate(['required', 'integer'])]
    public $subject_id = '';

    #[Validate(['nullable'])]
    public $type = '';

    #[Validate(['required'])]
    public $activity = '';

    #[Validate(['nullable', 'boolean'])]
    public $is_flagged = '';

    #[Validate(['nullable', 'date'])]
    public $entered_at = '';
}
