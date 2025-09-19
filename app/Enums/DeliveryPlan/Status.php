<?php

namespace App\Enums\DeliveryPlan;

enum Status: string
{
    case pending = 'pending';
    case approved = 'approved';
    case in_progress = 'in_progress';
    case completed = 'completed';
    case cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::pending => 'Pending',
            self::approved => 'Approved',
            self::in_progress => 'In Progress',
            self::completed => 'Completed',
            self::cancelled => 'Cancelled',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::pending => 'yellow',
            self::approved => 'teal',
            self::in_progress => 'blue',
            self::completed => 'green',
            self::cancelled => 'zinc',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::pending => 'fa-clock',
            self::approved => 'fa-thumbs-up',
            self::in_progress => 'fa-spinner',
            self::completed => 'fa-check',
            self::cancelled => 'fa-times',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::pending => 'Awaiting approval or start.',
            self::approved => 'Approved and ready to execute.',
            self::in_progress => 'Currently being executed.',
            self::completed => 'Successfully completed.',
            self::cancelled => 'Cancelled and will not proceed.',
        };
    }
}
