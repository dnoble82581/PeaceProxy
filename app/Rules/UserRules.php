<?php

namespace App\Rules;

use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserRules
{
    public static function forForm(?User $user, int $tenantId): array
    {
        $uniqueEmail = Rule::unique(User::class, 'email')
            ->where(fn ($query) => $query->where('tenant_id', $tenantId));

        if ($user?->exists) {
            $uniqueEmail = $uniqueEmail->ignore($user->id);
        }

        return [
            'name' => 'required|string|max:255',
            'email' => ['required', 'email:rfc,dns', 'max:255', $uniqueEmail],
            'password' => [
                $user?->exists ? 'nullable' : 'required', 'string', 'min:8', 'confirmed', Password::defaults()],
            'is_active' => 'required|boolean',
            'dark_mode' => 'required|boolean',
            'locale' => 'required|string',
            'timezone' => 'required|string',
            'avatar_path' => 'nullable|string',
            'permissions' => 'nullable|string',
            'rank_or_title' => 'nullable|string',
            'primary_team_id' => 'nullable|integer',
            'badge_number' => 'nullable|string',
            'license_number' => 'nullable|string',
            'department' => 'nullable|string',
            'phone' => 'nullable|string',
            'extension' => 'nullable|string',
            'alternate_email' => 'nullable|string|email|max:255',
            'last_login_at' => 'nullable|date',
            'last_login_ip' => 'nullable|string',
            'trial_ends_at' => 'nullable|date',
            'pm_type' => 'nullable|string',
            'pm_last_four' => 'nullable|string',
            'two_factor_secret' => 'nullable|string',
            'two_factor_recovery_codes' => 'nullable|string',
            'remember_token' => 'nullable|string',
            'stripe_id' => 'nullable|string',
        ];
    }
}
