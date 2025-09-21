<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

class CreateDemandForm extends Form
{
    #[Validate(['required', 'integer'])]
    public $tenant_id = '';

    #[Validate(['required', 'integer'])]
    public $subject_id = '';

    #[Validate(['required', 'integer'])]
    public $negotiation_id = '';

    #[Validate(['nullable', 'string'])]
    public $created_by = '';

    #[Validate(['nullable', 'string'])]
    public $updated_by = '';

    #[Validate(['required'])]
    public $title = '';

    #[Validate(['required'])]
    public $content = '';

    #[Validate(['required'])]
    public $category = '';

    #[Validate(['required'])]
    public $status = '';

    #[Validate(['required'])]
    public $priority_level = '';

    #[Validate(['required'])]
    public $channel = '';

    #[Validate(['nullable', 'date'])]
    public $deadline_date = null;

    #[Validate(['nullable'])]
    public $deadline_time = null;

    #[Validate(['nullable', 'date'])]
    public $communicated_at = null;

    #[Validate(['nullable', 'date'])]
    public $responded_at = null;

    #[Validate(['nullable', 'date'])]
    public $resolved_at = null;

    #[Validate(['nullable', 'date'])]
    public $deleted_at = null;

    #[Validate(['nullable', 'date'])]
    public $created_at = null;
}
