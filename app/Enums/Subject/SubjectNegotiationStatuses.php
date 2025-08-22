<?php

namespace App\Enums\Subject;

enum SubjectNegotiationStatuses: string
{
    case active = 'active';
    case inactive = 'inactive';
    case left = 'left';
    case in_custody = 'in custody';
    case unknown = 'unknown';

    public function label(): string
    {
        return match ($this) {
            self::active => 'Active',
            self::inactive => 'InActive',
            self::left => 'Left',
            self::in_custody => 'In Custody',
            self::unknown => 'Unknown',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::active => 'bg-green-500',
            self::inactive => 'bg-blue-500',
            self::left => 'bg-red-500',
            self::in_custody => 'bg-yellow-500',
            self::unknown => 'bg-violet-500',
        };
    }
}
