<?php

namespace App\Enums\Activity;

enum ActivityType: string
{
    case subject_action = 'subject_action';
    case negotiator_action = 'negotiator_action';
    case system_event = 'system_event';
    case note = 'note';
    case rfi = 'rfi';
    case timeline_entry = 'timeline_entry';

    public function label(): string
    {
        return match ($this) {
            self::subject_action => 'Subject Action',
            self::negotiator_action => 'Negotiator Action',
            self::system_event => 'System Event',
            self::note => 'Note',
            self::rfi => 'RFI',
            self::timeline_entry => 'Timeline Entry',
        };
    }
}
