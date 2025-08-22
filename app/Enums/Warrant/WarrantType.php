<?php

namespace App\Enums\Warrant;

enum WarrantType: string
{
    case arrest = 'arrest';
    case search = 'search';
    case bench = 'bench';
    case fugitive = 'fugitive';
    case parole_violation = 'parole_violation';
    case other = 'other';
    case unknown = 'unknown';

    public function label(): string
    {
        return match ($this) {
            self::arrest => 'Arrest',
            self::search => 'Search',
            self::bench => 'Bench',
            self::fugitive => 'Fugitive',
            self::parole_violation => 'Parole Violation',
            self::other => 'Other',
            self::unknown => 'Unknown',
        };
    }
}
