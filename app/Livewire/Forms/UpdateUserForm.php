<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

class UpdateUserForm extends Form
{
    #[Validate(['required'])]
    public $name = '';

    #[Validate(['required', 'email', 'max:254'])]
    public $email = '';

    // Password is optional when updating; validate only when provided and require confirmation
    #[Validate(['nullable', 'string', 'min:8', 'confirmed'])]
    public $password = '';

    // This property is required for the `confirmed` rule to work within a Livewire Form
    #[Validate(['nullable', 'string', 'min:8'])]
    public $password_confirmation = '';

    #[Validate(['nullable', 'date'])]
    public $email_verified_at = null;

    #[Validate(['required', 'integer'])]
    public $tenant_id = '';

    #[Validate(['nullable'])]
    public $permissions = '';

    #[Validate(['nullable'])]
    public $rank_or_title = '';

    #[Validate(['nullable', 'integer'])]
    public $primary_team_id = '';

    #[Validate(['nullable'])]
    public $badge_number = '';

    #[Validate(['nullable'])]
    public $license_number = '';

    #[Validate(['nullable'])]
    public $department = '';

    #[Validate(['nullable'])]
    public $phone = '';

    #[Validate(['nullable'])]
    public $extension = '';

    #[Validate(['nullable', 'email', 'max:254'])]
    public $alternate_email = '';

    #[Validate(['nullable', 'date'])]
    public $last_login_at = '';

    #[Validate(['nullable'])]
    public $last_login_ip = '';

    #[Validate(['nullable'])]
    public $avatar_path = '';

    #[Validate(['required'])]
    public $locale = '';

    #[Validate(['required'])]
    public $timezone = '';

    #[Validate(['boolean'])]
    public $dark_mode = '';

    #[Validate(['boolean'])]
    public $is_active = '';

    #[Validate(['nullable'])]
    public $two_factor_secret = '';

    #[Validate(['nullable'])]
    public $two_factor_recovery_codes = '';

    #[Validate(['nullable'])]
    public $stripe_id = '';

    #[Validate(['nullable'])]
    public $pm_type = '';

    #[Validate(['nullable'])]
    public $pm_last_four = '';

    #[Validate(['nullable', 'date'])]
    public $trial_ends_at = '';
}
