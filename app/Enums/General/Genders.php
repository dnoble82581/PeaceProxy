<?php

namespace App\Enums\General;

enum Genders: string
{
    case Male = 'male';
    case Female = 'female';
    case NonBinary = 'non-binary';
    case Other = 'other';
    case PreferNotToSay = 'prefer-not-to-say';

    public function label(): string
    {
        return match ($this) {
            self::Male => 'Male',
            self::Female => 'Female',
            self::NonBinary => 'Non-binary',
            self::Other => 'Other',
            self::PreferNotToSay => 'Prefer not to say',
        };
    }
}
