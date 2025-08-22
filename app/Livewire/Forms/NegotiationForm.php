<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

class NegotiationForm extends Form
{
    #[Validate(['required', 'integer'])]
    public $tenant_id = '';

    #[Validate(['required'])]
    public $title = '';

    #[Validate(['nullable'])]
    public $summary = '';

    #[Validate(['nullable', 'date'])]
    public $started_at = null;

    #[Validate(['nullable', 'date'])]
    public $ended_at = null;

    #[Validate(['nullable'])]
    public $location = '';

    #[Validate(['nullable'])]
    public $location_address = '';

    #[Validate(['nullable'])]
    public $location_city = '';

    #[Validate(['nullable'])]
    public $location_state = '';

    #[Validate(['nullable', 'integer'])]
    public $location_zip = '';

    #[Validate(['required'])]
    public $status = '';

    #[Validate(['required'])]
    public $type = '';

    #[Validate(['nullable'])]
    public $initial_complaint = '';

    #[Validate(['nullable'])]
    public $negotiation_strategy = '';

    #[Validate(['nullable', 'integer'])]
    public $duration_minutes = null;

    #[Validate(['nullable'])]
    public $tags = '';
}
