<?php

namespace App\Enums\Demand;

enum DemandCategories: string
{
    case substantive = 'substantive';
    case expressive = 'expressive';
    case secondary = 'secondary';

    public function label(): string
    {
        return match ($this) {
            self::substantive => 'Substantive',
            self::expressive => 'Expressive',
            self::secondary => 'Secondary',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::substantive => 'sky',
            self::expressive => 'red',
            self::secondary => 'teal',
        };
    }

    public function description()
    {
        return match ($this) {
            self::substantive => 'Substantive demands are related directly to the motivation and or resolution',
            self::expressive => 'Expressive demands provide insight into subject\'s emotional status and behavior',
            self::secondary => 'All other demands such as food, cigarettes, beer, etc.'
        };
    }
}
