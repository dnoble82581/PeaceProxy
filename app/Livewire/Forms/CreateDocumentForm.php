<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

class CreateDocumentForm extends Form
{
    #[Validate(['required', 'integer'])]
    public $tenant_id = '';

    #[Validate(['nullable', 'integer'])]
    public $negotiation_id = null;

    #[Validate(['required', 'string'])]
    public $documentable_type = '';

    #[Validate(['required', 'integer'])]
    public $documentable_id = '';

    #[Validate(['required', 'string', 'max:255'])]
    public $name = '';

    #[Validate(['nullable', 'string', 'max:255'])]
    public $category = '';

    #[Validate(['nullable', 'string'])]
    public $description = '';

    #[Validate(['boolean'])]
    public $is_private = true;

    #[Validate(['nullable', 'array'])]
    public $tags = [];

    // File will be handled separately in the component
    public $file = null;
}
