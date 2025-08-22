<?php

namespace App\Enums\Trigger;

enum TriggerCategories: string
{
    case personal = 'personal';
    case sharedInterest = 'shared_interest';
    case family = 'family';
    case work = 'work';
    case health = 'health';
    case financial = 'financial';
    case legal = 'legal';
    case other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::personal => 'Personal',
            self::sharedInterest => 'Shared Interest',
            self::family => 'Family',
            self::work => 'Work',
            self::health => 'Health',
            self::financial => 'Financial',
            self::legal => 'Legal',
            self::other => 'Other',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::personal => 'sky',
            self::sharedInterest => 'blue',
            self::family => 'cyan',
            self::work => 'teal',
            self::health => 'red',
            self::financial => 'green',
            self::legal => 'emerald',
            self::other => 'zinc',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::personal => 'fa-user',
            self::sharedInterest => 'fa-users',
            self::family => 'fa-home',
            self::work => 'fa-briefcase',
            self::health => 'fa-heartbeat',
            self::financial => 'fa-money-bill',
            self::legal => 'fa-gavel',
            self::other => 'fa-question-circle',
        };
    }
}
