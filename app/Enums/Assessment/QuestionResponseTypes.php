<?php

namespace App\Enums\Assessment;

enum QuestionResponseTypes: string
{
    case text = 'text';
    case number = 'number';
    case boolean = 'boolean';
    case rating = 'rating';
    case textarea = 'textarea';
    case select = 'select';
    case multiselect = 'multiselect';
    case checkbox = 'checkbox';
    case radio = 'radio';
    case date = 'date';
    case time = 'time';
    case datetime = 'datetime';
    case file = 'file';

    public function label(): string
    {
        return match ($this) {
            self::text => 'Text',
            self::number => 'Number',
            self::boolean => 'Boolean',
            self::rating => 'Rating',
            self::textarea => 'Text Area',
            self::select => 'Select Dropdown',
            self::multiselect => 'Multi-Select',
            self::checkbox => 'Checkbox',
            self::radio => 'Radio Button',
            self::date => 'Date',
            self::time => 'Time',
            self::datetime => 'Date & Time',
            self::file => 'File Upload',
        };
    }
}
