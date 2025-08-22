<?php

namespace App\Enums\Warrant;

enum BondType: string
{
    case Cash = 'cash';
    case Surety = 'surety';
    case Recognizance = 'recognizance';
    case Property = 'property';
    case Unsecured = 'unsecured';
    case NoBond = 'no_bond';

    public function label(): string
    {
        return match ($this) {
            self::Cash => 'Cash',
            self::Surety => 'Surety',
            self::Recognizance => 'Recognizance',
            self::Property => 'Property',
            self::Unsecured => 'Unsecured',
            self::NoBond => 'No Bond',
        };
    }
}
