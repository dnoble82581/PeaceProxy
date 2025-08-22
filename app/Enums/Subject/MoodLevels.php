<?php

namespace App\Enums\Subject;

enum MoodLevels: int
{
    case Suicidal = 1;
    case SeverelyDepressed = 2;
    case Depressed = 3;
    case Sad = 4;
    case Low = 5;
    case Neutral = 6;
    case SlightlyHappy = 7;
    case Happy = 8;
    case Euphoric = 9;
    case Hypomanic = 10;
    case Manic = 11;

    public function label(): string
    {
        return match ($this) {
            self::Suicidal => 'Suicidal',
            self::SeverelyDepressed => 'Severely Depressed',
            self::Depressed => 'Depressed',
            self::Sad => 'Sad',
            self::Low => 'Low',
            self::Neutral => 'Neutral',
            self::SlightlyHappy => 'Slightly Happy',
            self::Happy => 'Happy',
            self::Euphoric => 'Euphoric',
            self::Hypomanic => 'Hypomanic',
            self::Manic => 'Manic',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Suicidal => '😵‍💫',
            self::SeverelyDepressed => '😭',
            self::Depressed => '😔',
            self::Sad => '🙁',
            self::Low => '😐',
            self::Neutral => '🙂',
            self::SlightlyHappy => '😊',
            self::Happy => '😃',
            self::Euphoric => '😄',
            self::Hypomanic => '🤪',
            self::Manic => '😵‍',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Suicidal => 'Feeling life is unbearable or having thoughts of self-harm.',
            self::SeverelyDepressed => 'Overwhelmed with hopelessness and emotional pain.',
            self::Depressed => 'Persistent sadness, low energy, and lack of interest.',
            self::Sad => 'Feeling down, discouraged, or upset.',
            self::Low => 'Slightly off, tired, or lacking motivation.',
            self::Neutral => 'Emotionally balanced and steady.',
            self::SlightlyHappy => 'Mild contentment or light enjoyment.',
            self::Happy => 'Positive mood, engaged, and emotionally stable.',
            self::Euphoric => 'High energy, strong positivity, almost giddy.',
            self::Hypomanic => 'Elevated mood with increased activity and reduced need for sleep.',
            self::Manic => 'Extremely elevated or irritable mood with racing thoughts and risky behavior.',
        };
    }
}
