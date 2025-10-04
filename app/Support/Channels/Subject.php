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

    public const SUBJECT_ASSESSMENT_PATTERN = 'subject.{subjectId}.assessment';

    public const SUBJECT_CONTACT_PATTERN = 'subject.{subjectId}.contact';

    public const SUBJECT_SOR_CHECK_PATTERN = 'subject.{subjectId}.sor-check';

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

    public static function subjectAssessment(int $id): string
    {
        return "subject.$id.assessment";
    }

    public static function subjectContact(int $id): string
    {
        return "subject.$id.contact";
    }

    public static function subjectSORCheck(int $id): string
    {
        return "subject.$id.sor-check";
    }
}
