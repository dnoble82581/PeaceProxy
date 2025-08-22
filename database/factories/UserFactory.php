<?php

namespace Database\Factories;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        $permissions = ['admin', 'user', 'superadmin', 'observer'];
        $departments = ['Patrol', 'Investigations', 'Administration', 'Communications', 'Records', 'Crisis Response', 'Community Services'];

        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'tenant_id' => Tenant::factory(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),

            // Role and Position
            'permissions' => fake()->randomElement($permissions),
            'rank_or_title' => fake()->optional(0.8)->jobTitle(),

            // Identity / Credentials
            'badge_number' => fake()->optional(0.6)->regexify('[A-Z]{1,2}[0-9]{3,5}'),
            'license_number' => fake()->optional(0.4)->regexify('[A-Z]{2}[0-9]{6}'),
            'department' => fake()->optional(0.7)->randomElement($departments),

            // Contact Details
            'phone' => fake()->optional(0.8)->phoneNumber(),
            'extension' => fake()->optional(0.3)->numerify('####'),
            'alternate_email' => fake()->optional(0.4)->safeEmail(),

            // Activity Tracking
            'last_login_at' => fake()->optional(0.7)->dateTimeThisMonth(),
            'last_login_ip' => fake()->optional(0.7)->ipv4(),

            // Avatar and Preferences
            'avatar_path' => fake()->optional(0.5)->imageUrl(200, 200, 'people'),
            'locale' => 'en',
            'timezone' => 'America/Chicago',
            'dark_mode' => fake()->boolean(30),

            // Status
            'is_active' => true,

            // MFA / Security
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'remember_token' => Str::random(10),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
