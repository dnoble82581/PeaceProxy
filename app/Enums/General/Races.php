<?php

namespace App\Enums\General;

enum Races: string
{
    case white = 'white';
    case black = 'black';
    case hispanic = 'hispanic';
    case asian = 'asian';
    case native_american = 'native_american';
    case pacific_islander = 'pacific_islander';
    case multiracial = 'multiracial';
    case other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::white => 'White',
            self::black => 'Black or African American',
            self::hispanic => 'Hispanic or Latino',
            self::asian => 'Asian',
            self::native_american => 'Native American or Alaska Native',
            self::pacific_islander => 'Native Hawaiian or Pacific Islander',
            self::multiracial => 'Multiracial',
            self::other => 'Other',
        };
    }
}
