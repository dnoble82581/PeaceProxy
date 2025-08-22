<?php

namespace App\Enums\Trigger;

enum TriggerSensitivityLevels: string
{
    case low = 'low';
    case medium = 'medium';
    case high = 'high';

    public function label(): string
    {
        return match ($this) {
            self::low => 'Low',
            self::medium => 'Medium',
            self::high => 'High',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::low => 'bg-green-500',
            self::medium => 'bg-yellow-500',
            self::high => 'bg-red-500',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::low => 'fa-shield',
            self::medium => 'fa-shield-alt',
            self::high => 'fa-exclamation-triangle',
        };
    }
}
