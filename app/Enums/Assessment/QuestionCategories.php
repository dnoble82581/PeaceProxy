<?php

namespace App\Enums\Assessment;

enum QuestionCategories: string
{
    case subject = 'subject';
    case tactical = 'tactical';
    case operational = 'operational';
    case strategic = 'strategic';
    case environmental = 'environmental';
    case team = 'team';
    case communication = 'communication';
    case general = 'general';

    public function label(): string
    {
        return match ($this) {
            self::subject => 'Subject Risk',
            self::tactical => 'tactical Risk',
            self::operational => 'Operational Risk',
            self::strategic => 'Strategic Risk',
            self::environmental => 'Environmental Risk',
            self::team => 'Team Risk',
            self::communication => 'Communication Risk',
            self::general => 'General',
        };
    }
}
