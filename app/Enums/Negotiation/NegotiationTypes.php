<?php

namespace App\Enums\Negotiation;

enum NegotiationTypes: string
{
    case hostage = 'hostage';
    case suicidal = 'suicidal';
    case barricaded = 'barricaded';
    case unknown = 'unknown';

    public function label()
    {
        return match ($this) {
            self::hostage => 'Hostage',
            self::suicidal => 'Suicidal',
            self::barricaded => 'Barricaded',
            self::unknown => 'Unknown',
        };
    }

    public function color()
    {
        return match ($this) {
            self::hostage => 'bg-blue-500',
            self::suicidal => 'bg-green-500',
            self::barricaded => 'bg-red-500',
            self::unknown => 'bg-yellow-500',
        };
    }

    public function description()
    {
        return match ($this) {
            self::hostage => 'A negotiation involving a hostage situation.',
            self::suicidal => 'A negotiation where the subject is threatening self-harm or suicide.',
            self::barricaded => 'A negotiation involving a barricaded subject who may be armed or otherwise dangerous.',
            self::unknown => 'A negotiation involving an unknown or unclear situation.',
        };

    }
}
