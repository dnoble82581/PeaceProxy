<?php

namespace App\Enums\General;

enum Channels: string
{
    case email = 'email';
    case sms = 'sms';
    case phone = 'phone';
    case chat = 'chat';
    case web = 'web';
    case other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::email => 'Email',
            self::sms => 'SMS',
            self::phone => 'Phone',
            self::chat => 'Chat',
            self::web => 'Web',
            self::other => 'Other',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::email => 'envelope',
            self::sms => 'chat-bubble-bottom-center',
            self::phone => 'phone',
            self::chat => 'chat-bubble-left-ellipsis',
            self::web => 'globe-alt',
            self::other => 'question-mark-circle',
        };
    }
}
