<?php

namespace App\Enums\General;

enum Languages: string
{
    case english = 'english';
    case spanish = 'spanish';
    case chinese = 'chinese';
    case tagalog = 'tagalog';
    case vietnamese = 'vietnamese';
    case arabic = 'arabic';
    case french = 'french';
    case korean = 'korean';
    case russian = 'russian';
    case german = 'german';
    case hindi = 'hindi';
    case portuguese = 'portuguese';
    case italian = 'italian';
    case polish = 'polish';
    case japanese = 'japanese';
    case other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::english => 'English',
            self::spanish => 'Spanish',
            self::chinese => 'Chinese',
            self::tagalog => 'Tagalog',
            self::vietnamese => 'Vietnamese',
            self::arabic => 'Arabic',
            self::french => 'French',
            self::korean => 'Korean',
            self::russian => 'Russian',
            self::german => 'German',
            self::hindi => 'Hindi',
            self::portuguese => 'Portuguese',
            self::italian => 'Italian',
            self::polish => 'Polish',
            self::japanese => 'Japanese',
            self::other => 'Other',
        };
    }
}
