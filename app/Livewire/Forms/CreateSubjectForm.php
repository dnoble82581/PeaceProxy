<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

class CreateSubjectForm extends Form
{
    #[Validate(['required', 'integer'])]
    public $tenant_id = '';

    #[Validate(['required'])]
    public $name = '';

    #[Validate(['nullable'])]
    public $alias = '';

    #[Validate(['nullable', 'date'])]
    public $date_of_birth = '';

    #[Validate(['nullable'])]
    public $gender = '';

    #[Validate(['nullable'])]
    public $height = '';

    #[Validate(['nullable'])]
    public $weight = '';

    #[Validate(['nullable'])]
    public $hair_color = '';

    #[Validate(['nullable'])]
    public $eye_color = '';

    #[Validate(['nullable'])]
    public $identifying_features = '';

    #[Validate(['nullable'])]
    public $phone = '';

    #[Validate(['nullable', 'email', 'max:254'])]
    public $email = '';

    #[Validate(['nullable'])]
    public $address = '';

    #[Validate(['nullable'])]
    public $city = '';

    #[Validate(['nullable'])]
    public $state = '';

    #[Validate(['nullable'])]
    public $zip = '';

    #[Validate(['nullable'])]
    public $country = '';

    #[Validate(['nullable'])]
    public $occupation = '';

    #[Validate(['nullable'])]
    public $employer = '';

    #[Validate(['nullable'])]
    public $mental_health_history = '';

    #[Validate(['nullable'])]
    public $criminal_history = '';

    #[Validate(['nullable'])]
    public $substance_abuse_history = '';

    #[Validate(['nullable'])]
    public $known_weapons = '';

    #[Validate(['nullable'])]
    public $risk_factors = '';

    #[Validate(['nullable'])]
    public $notes = '';

    #[Validate(['required'])]
    public $current_mood = '';

    #[Validate(['required'])]
    public $status = '';
}
