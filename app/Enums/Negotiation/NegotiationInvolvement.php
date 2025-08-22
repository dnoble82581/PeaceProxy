<?php

namespace App\Enums\Negotiation;

enum NegotiationInvolvement: string
{
    case active = 'active';
    case inactive = 'inactive';
    case unknown = 'unknown';
    case passive = 'passive';

    public function label(): string
    {
        return match ($this) {
            self::active => 'Active',
            self::inactive => 'Inactive',
            self::unknown => 'Unknown',
            self::passive => 'Passive',
        };
    }
}
