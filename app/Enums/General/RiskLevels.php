<?php

namespace App\Enums\General;

enum RiskLevels: string
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
            self::low => 'bg-sky-600 dark:bg-sky-700',
            self::medium => 'bg-orange-600 dark:bg-orange-700',
            self::high => 'bg-pink-600 dark:bg-pink-700',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::low => 'fa-circle',
            self::medium => 'fa-circle',
            self::high => 'fa-circle',
        };
    }
}
