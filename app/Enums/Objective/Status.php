<?php

namespace App\Enums\Objective;

enum Status: string
{
    case in_progress = 'in_progress';
    case completed = 'completed';
    case cancelled = 'cancelled';
    case blocked = 'blocked';
    case pending = 'pending';

    public function label(): string
    {
        return match ($this) {
            self::in_progress => 'In Progress',
            self::completed => 'Completed',
            self::cancelled => 'Cancelled',
            self::blocked => 'Blocked',
            self::pending => 'Pending',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::in_progress => 'blue',
            self::completed => 'green',
            self::cancelled => 'zinc',
            self::blocked => 'red',
            self::pending => 'yellow',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::in_progress => 'fa-spinner',
            self::completed => 'fa-check',
            self::cancelled => 'fa-times',
            self::blocked => 'fa-ban',
            self::pending => 'fa-clock',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::in_progress => 'The objective is currently being worked on.',
            self::completed => 'The objective has been successfully completed.',
            self::cancelled => 'The objective has been cancelled and is no longer being pursued.',
            self::blocked => 'The objective is blocked and cannot proceed until issues are resolved.',
            self::pending => 'The objective is waiting to be started or approved.',
        };
    }
}
