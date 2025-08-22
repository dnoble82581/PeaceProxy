<?php

namespace App\Enums\General;

enum ContactTypes: string
{
    case primary = 'primary';
    case secondary = 'secondary';
    case tertiary = 'tertiary';
    case other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::primary => 'Primary',
            self::secondary => 'Secondary',
            self::tertiary => 'Tertiary',
            self::other => 'Other',
        };
    }
}
