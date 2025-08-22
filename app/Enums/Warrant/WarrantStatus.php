<?php

namespace App\Enums\Warrant;

enum WarrantStatus: string
{
    case active = 'active';
    case expired = 'expired';
    case revoked = 'revoked';
    case cancelled = 'cancelled';
    case pending = 'pending';
    case arrested = 'arrested';
    case released = 'released';
    case released_pending_arrest = 'released_pending_arrest';
    case released_pending_search = 'released_pending_search';

    public function label(): string
    {
        return match ($this) {
            self::active => 'Active',
            self::expired => 'Expired',
            self::revoked => 'Revoked',
            self::cancelled => 'Cancelled',
            self::pending => 'Pending',
            self::arrested => 'Arrested',
            self::released => 'Released',
            self::released_pending_arrest => 'Released Pending Arrest',
            self::released_pending_search => 'Released Pending Search',

        };
    }
}
