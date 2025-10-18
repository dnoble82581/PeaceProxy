<?php

namespace App\Livewire\Forms\User;

use App\Models\Tenant;
use App\Models\User;
use App\Rules\UserRules;
use App\Services\User\CreateUserService;
use App\Services\User\UpdateUserService;
use Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rules\Unique;
use Livewire\Attributes\Locked;
use Livewire\Form;

class UserForm extends Form
{
    public ?User $user = null;

    #[Locked]
    public ?int $tenantId = null;

    public string $name = '';

    public string $email = '';

    public ?string $password = null;

    public ?string $password_confirmation = null;

    public ?string $email_verified_at = null;

    public string $permissions = '';

    public string $rank_or_title = '';

    public ?int $primary_team_id = null;

    public string $badge_number = '';

    public string $license_number = '';

    public string $department = '';

    public string $phone = '';

    public string $extension = '';

    public string $alternate_email = '';

    public string $last_login_at = '';

    public string $last_login_ip = '';

    public string $avatar_path = '';

    public string $locale = 'en';

    public string $timezone = 'America/Chicago';

    public bool $dark_mode = false;

    public bool $is_active = true;

    public string $two_factor_secret = '';

    public string $two_factor_recovery_codes = '';

    public string $remember_token = '';

    public ?string $stripe_id = null;

    public ?string $pm_type = null;

    public ?string $pm_last_four = null;

    public ?string $trial_ends_at = null;

    public function setUser(?User $user, int $tenantId): void
    {
        $this->user = $user;
        $this->tenantId = $tenantId;

        if ($user) {
            $this->fill($user);
        }
    }

    /**
     * @return array<string, array<int, Password|Unique|string|null>|string>
     */
    public function rules(): array
    {
        $tenantId = $this->tenantId ?? (tenant()?->id ?? 0);

        return UserRules::forForm($this->user, $tenantId);
    }

    public function create(): User
    {
        $this->validate();

        $data = $this->payload();
        // Always hash password on create
        $data['password'] = Hash::make((string) ($data['password'] ?? ''));

        /** @var CreateUserService $service */
        $service = app(CreateUserService::class);
        $tenantId = $this->tenantId ?? tenant()?->id;
        $tenant = Tenant::findOrFail((int) $tenantId);

        return $service->createUserFromTenant($tenant, $data);
    }

    /**
     * @return array<string, bool|int|string>
     */
    public function payload(): array
    {
        return [
            'tenant_id' => $this->tenantId ?? \tenant()->id,
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'is_active' => $this->is_active,
            'dark_mode' => $this->dark_mode,
            'locale' => $this->locale ?? 'en',
            'timezone' => $this->timezone ?? 'America/Chicago',
            'avatar_path' => $this->avatar_path,
            'permissions' => $this->permissions === '' ? 'user' : $this->permissions,
            'rank_or_title' => $this->rank_or_title,
            'primary_team_id' => $this->primary_team_id,
            'badge_number' => $this->badge_number,
            'license_number' => $this->license_number,
            'department' => $this->department,
            'phone' => $this->phone,
            'extension' => $this->extension,
            'alternate_email' => $this->alternate_email,
            'last_login_at' => $this->last_login_at === '' ? now() : $this->last_login_at,
            'last_login_ip' => $this->last_login_ip === '' ? request()->ip() : $this->last_login_ip,
            'trial_ends_at' => $this->trial_ends_at,
            'pm_type' => $this->pm_type,
            'pm_last_four' => $this->pm_last_four,
            'two_factor_secret' => $this->two_factor_secret,
            'two_factor_recovery_codes' => $this->two_factor_recovery_codes,
            'remember_token' => $this->remember_token,
            'stripe_id' => $this->stripe_id,
        ];
    }

    public function update(): User
    {
        $this->validate();

        $data = $this->payload();

        // If password is empty/null, keep existing (omit from update payload). If provided, hash the new password.
        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make((string) $data['password']);
        }

        /** @var UpdateUserService $service */
        $service = app(UpdateUserService::class);

        return $service->updateUser($this->user, $data);
    }
}
