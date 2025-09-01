<?php

namespace App\Enums\User;

enum UserPermission: string
{
    case Admin = 'admin';
    case User = 'user';
    case SuperAdmin = 'superadmin';
    case Observer = 'observer';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Administrator',
            self::User => 'User',
            self::SuperAdmin => 'Super Administrator',
            self::Observer => 'Observer',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Admin => 'Has access to administrative features and can manage users within their tenant.',
            self::User => 'Standard user with basic access to the application features.',
            self::SuperAdmin => 'Has full access to all features and can manage all tenants and users.',
            self::Observer => 'Has read-only access to view information but cannot make changes.',
        };
    }
}
