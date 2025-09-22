<?php

namespace App\Support\Channels;

final class Subject
{
    // Patterns (used in routes/channels.php)
    public const SUBJECT_PATTERN = 'subject.{subjectId}';

    public const SUBJECT_MOOD_PATTERN = 'subject.{subjectId}.mood';

    public const SUBJECT_WARNING_PATTERN = 'subject.{subjectId}.warning';

    public const SUBJECT_WARRANT_PATTERN = 'subject.{subjectId}.warrant';

    public const SUBJECT_DOCUMENT_PATTERN = 'subject.{subjectId}.document';

    // Concrete builders (used in events / listeners at runtime)
    public static function subject(int $id): string
    {
        return "subject.$id";
    }

    public static function subjectMood(int $id): string
    {
        return "subject.$id.mood";
    }

    public static function subjectWarning(int $id): string
    {
        return "subject.$id.warning";
    }

    public static function subjectWarrant(int $id): string
    {
        return "subject.$id.warrant";
    }

    public static function subjectDocument(int $id): string
    {
        return "subject.$id.document";
    }
}
