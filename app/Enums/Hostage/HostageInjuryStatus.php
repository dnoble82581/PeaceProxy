<?php

namespace App\Enums\Hostage;

enum HostageInjuryStatus: string
{
    case none = 'none';
    case minor = 'minor';
    case moderate = 'moderate';
    case severe = 'severe';
    case critical = 'critical';
    case unknown = 'unknown';

    public function label(): string
    {
        return match ($this) {
            self::none => 'None',
            self::minor => 'Minor',
            self::moderate => 'Moderate',
            self::severe => 'Severe',
            self::critical => 'Critical',
            self::unknown => 'Unknown',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::none => 'bg-green-500',
            self::minor => 'bg-blue-500',
            self::moderate => 'bg-yellow-500',
            self::severe => 'bg-orange-500',
            self::critical => 'bg-red-500',
            self::unknown => 'bg-gray-500',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::none => 'The hostage has no apparent injuries.',
            self::minor => 'The hostage has minor injuries that do not require immediate medical attention.',
            self::moderate => 'The hostage has moderate injuries that require medical attention but are not life-threatening.',
            self::severe => 'The hostage has severe injuries that require immediate medical attention.',
            self::critical => 'The hostage has critical injuries that are life-threatening and require urgent medical intervention.',
            self::unknown => 'The injury status of the hostage is unknown or unconfirmed.',
        };
    }
}
