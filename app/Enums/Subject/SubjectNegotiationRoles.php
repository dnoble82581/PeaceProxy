<?php

namespace App\Enums\Subject;

enum SubjectNegotiationRoles: string
{
    case primary = 'primary';
    case secondary = 'secondary';
    case tertiary = 'tertiary';

    public function label(): string
    {
        return match ($this) {
            self::primary => 'Primary',
            self::secondary => 'Secondary',
            self::tertiary => 'Tertiary',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::primary => 'bg-green-500',
            self::secondary => 'bg-blue-500',
            self::tertiary => 'bg-rose-500',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::primary => 'This is the primary or leading subject in the negotiation.',
            self::secondary => 'This subject plays a supporting or secondary role in the negotiation',
            self::tertiary => 'This subject has a tertiary or lower priority role in the negotiation.',
        };
    }
}
