<?php

namespace App\Enums\Objective;

enum Priority: string
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
            self::low => 'sky',
            self::medium => 'blue',
            self::high => 'red',
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
