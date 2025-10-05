<?php

namespace App\Enums\Team;

enum TeamDiscipline: string
{
    case Negotiation = 'negotiation';
    case Tactical = 'tactical';
    case Command = 'command';
    case Intel = 'intel';
    case Communications = 'communications';

    public static function options(): array
    {
        return array_map(
            fn (self $e) => ['label' => $e->label(), 'value' => $e->value],
            self::cases()
        );
    }

    public function label(): string
    {
        return match ($this) {
            self::Negotiation => 'Negotiation',
            self::Tactical => 'Tactical',
            self::Command => 'Command',
            self::Intel => 'Intel',
            self::Communications => 'Communications',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
