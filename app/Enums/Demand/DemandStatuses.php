<?php

namespace App\Enums\Demand;

enum DemandStatuses: string
{
    case pending = 'pending';
    case met = 'met';
    case rejected = 'rejected';
    case cancelled = 'cancelled';
    case expired = 'expired';
    case negotiating = 'negotiating';
    case approved = 'approved';

    public function label(): string
    {
        return match ($this) {
            self::pending => 'Pending',
            self::met => 'Met',
            self::rejected => 'Rejected',
            self::cancelled => 'Cancelled',
            self::expired => 'Expired',
            self::negotiating => 'Negotiating',
            self::approved => 'Approved',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::pending => 'The demand is pending approval.',
            self::met => 'The demand has been met.',
            self::rejected => 'The demand has been rejected.',
            self::cancelled => 'The demand has been cancelled.',
            self::expired => 'The demand has expired.',
            self::negotiating => 'The demand is currently being negotiated.',
            self::approved => 'The demand has been approved.',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::pending => 'yellow',
            self::met => 'green',
            self::rejected => 'red',
            self::cancelled => 'blue',
            self::expired => 'rose',
            self::negotiating => 'sky',
            self::approved => 'teal',
        };
    }
}
