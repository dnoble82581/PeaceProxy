<?php

namespace App\Enums\Hostage;

enum HostageSubjectRelation: string
{
    case family = 'family';
    case spouse = 'spouse';
    case child = 'child';
    case parent = 'parent';
    case sibling = 'sibling';
    case friend = 'friend';
    case colleague = 'colleague';
    case acquaintance = 'acquaintance';
    case stranger = 'stranger';
    case other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::family => 'Family Member',
            self::spouse => 'Spouse',
            self::child => 'Child',
            self::parent => 'Parent',
            self::sibling => 'Sibling',
            self::friend => 'Friend',
            self::colleague => 'Colleague',
            self::acquaintance => 'Acquaintance',
            self::stranger => 'Stranger',
            self::other => 'Other',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::family, self::spouse, self::child, self::parent, self::sibling => 'bg-red-500',
            self::friend => 'bg-blue-500',
            self::colleague => 'bg-green-500',
            self::acquaintance => 'bg-yellow-500',
            self::stranger => 'bg-gray-500',
            self::other => 'bg-purple-500',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::family => 'A general family member of the subject.',
            self::spouse => 'Married or domestic partner of the subject.',
            self::child => 'Son or daughter of the subject.',
            self::parent => 'Father or mother of the subject.',
            self::sibling => 'Brother or sister of the subject.',
            self::friend => 'A close personal friend of the subject.',
            self::colleague => 'A work colleague or business associate of the subject.',
            self::acquaintance => 'Someone the subject knows but not closely.',
            self::stranger => 'No prior relationship with the subject.',
            self::other => 'A relationship not covered by other categories.',
        };
    }
}
