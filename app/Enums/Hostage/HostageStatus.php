<?php

namespace App\Enums\Hostage;

enum HostageStatus: string
{
    case captive = 'captive';
    case freed = 'Freed';
    case deceased = 'Deceased';
    case unknown = 'Unknown';

    public function label(): string
    {
        return match ($this) {
            self::captive => 'Captive',
            self::freed => 'Freed',
            self::deceased => 'Deceased',
            self::unknown => 'Unknown',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::captive => 'bg-red-500',
            self::freed => 'bg-green-500',
            self::deceased => 'bg-gray-500',
            self::unknown => 'bg-yellow-500',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::captive => 'The hostage is currently being held captive.',
            self::freed => 'The hostage has been freed or released.',
            self::deceased => 'The hostage is deceased.',
            self::unknown => 'The status of the hostage is unknown or unconfirmed.',
        };
    }
}
