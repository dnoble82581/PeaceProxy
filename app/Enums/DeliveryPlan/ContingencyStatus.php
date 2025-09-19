<?php

namespace App\Enums\DeliveryPlan;

enum ContingencyStatus: string
{
    case draft = 'draft';
    case approved = 'approved';
    case in_progress = 'in_progress';
    case completed = 'completed';
    case cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::draft => 'Draft',
            self::approved => 'Approved',
            self::in_progress => 'In Progress',
            self::completed => 'Completed',
            self::cancelled => 'Cancelled',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::draft => 'zinc',
            self::approved => 'teal',
            self::in_progress => 'blue',
            self::completed => 'green',
            self::cancelled => 'zinc',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::draft => 'fa-file',
            self::approved => 'fa-thumbs-up',
            self::in_progress => 'fa-spinner',
            self::completed => 'fa-check',
            self::cancelled => 'fa-times',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::draft => 'Not yet finalized; subject to change.',
            self::approved => 'Approved and ready if needed.',
            self::in_progress => 'Currently being executed as a fallback.',
            self::completed => 'Executed and finished.',
            self::cancelled => 'Cancelled and will not be used.',
        };
    }
}
