<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

class CreateUserForm extends Form
{
    #[Validate(['required'])]
    public $name = '';

    #[Validate(['required', 'email', 'max:254'])]
    public $email = '';

    #[Validate(['required'])]
    public $password = '';

    #[Validate(['nullable', 'integer'])]
    public $tenant_id = '';

    #[Validate(['nullable'])]
    public $permissions = '';

    #[Validate(['nullable'])]
    public $rank_or_title = '';

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
    public $last_login_at = null;

    #[Validate(['nullable', 'integer'])]
    public $primary_team_id = null;

    #[Validate(['nullable'])]
    public $last_login_ip = '';

    #[Validate(['nullable'])]
    public $avatar_path = '';

    #[Validate(['required'])]
    public $locale = 'en';

    #[Validate(['required'])]
    public $timezone = 'America/Chicago';

    #[Validate(['boolean'])]
    public $dark_mode = false;

    #[Validate(['boolean'])]
    public $is_active = true;

    #[Validate(['nullable'])]
    public $two_factor_secret = '';

    #[Validate(['nullable'])]
    public $two_factor_recovery_codes = '';
}
