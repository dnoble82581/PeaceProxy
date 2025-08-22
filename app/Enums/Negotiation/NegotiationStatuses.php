<?php

namespace App\Enums\Negotiation;

enum NegotiationStatuses: string
{
    case active = 'active';
    case resolved = 'resolved';
    case failed = 'failed';
    case standby = 'standby';

    public function label(): string
    {
        return match ($this) {
            self::active => 'Active',
            self::resolved => 'Resolved',
            self::failed => 'Failed',
            self::standby => 'Standby',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::active => 'emerald',
            self::resolved => 'zinc',
            self::failed => 'red',
            self::standby => 'blue',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::active => 'fa-circle',
            self::resolved => 'fa-check',
            self::failed => 'fa-times',
            self::standby => 'fa-circle',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::active => 'The negotiation is currently in progress.',
            self::resolved => 'The negotiation has been successfully completed.',
            self::failed => 'The negotiation has failed and did not result in a resolution.',
            self::standby => 'The negotiation is on hold, awaiting further actions.',
        };
    }
}
