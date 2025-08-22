<?php

namespace App\Enums\Warning;

enum WarningTypes: string
{
    case medical = 'medical';
    case substanceAbuse = 'substance_abuse';
    case weapons = 'weapons';
    case mentalHealth = 'mental_health';
    case violence = 'violence';
    case suicidal = 'suicidal';
    case selfHarm = 'self_harm';
    case allergies = 'allergies';
    case medications = 'medications';
    case other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::medical => 'Medical',
            self::substanceAbuse => 'Substance Abuse',
            self::weapons => 'Weapons',
            self::mentalHealth => 'Mental Health',
            self::violence => 'Violence',
            self::suicidal => 'Suicidal',
            self::selfHarm => 'Self Harm',
            self::allergies => 'Allergies',
            self::medications => 'Medications',
            self::other => 'Other',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::medical => 'blue',
            self::substanceAbuse => 'purple',
            self::weapons => 'red',
            self::mentalHealth => 'indigo',
            self::violence => 'red',
            self::suicidal => 'red',
            self::selfHarm => 'orange',
            self::allergies => 'yellow',
            self::medications => 'green',
            self::other => 'gray',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::medical => 'fa-kit-medical',
            self::substanceAbuse => 'fa-pills',
            self::weapons => 'fa-gun',
            self::mentalHealth => 'fa-brain',
            self::violence => 'fa-hand-fist',
            self::suicidal => 'fa-skull',
            self::selfHarm => 'fa-hand-holding-heart',
            self::allergies => 'fa-virus',
            self::medications => 'fa-prescription-bottle-medical',
            self::other => 'fa-circle-info',
        };
    }
}
