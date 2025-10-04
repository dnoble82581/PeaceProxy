<?php

namespace App\Support\EventNames;

class SubjectEventNames
{
    public const MOOD_CREATED = 'MoodCreated';

    public const SUBJECT_UPDATED = 'SubjectUpdated';

    public const WARNING_CREATED = 'WarningCreated';
    public const WARNING_DELETED = 'WarningDeleted';
    public const WARNING_UPDATED = 'WarningUpdated';
    public const WARRANT_UPDATED = 'WarrantUpdated';
    public const WARRANT_CREATED = 'WarrantCreated';
    public const WARRANT_DELETED = 'WarrantDeleted';

    public const DOCUMENT_CREATED = 'DocumentCreated';
    public const DOCUMENT_UPDATED = 'DocumentUpdated';
    public const DOCUMENT_DELETED = 'DocumentDeleted';

    public const ASSESSMENT_CREATED = 'AssessmentCreated';
    public const ASSESSMENT_UPDATED = 'AssessmentUpdated';
    public const ASSESSMENT_DELETED = 'AssessmentDeleted';
    public const ASSESSMENT_COMPLETED = 'AssessmentCompleted';

    public const CONTACT_CREATED = 'ContactCreated';
    public const CONTACT_UPDATED = 'ContactUpdated';
    public const CONTACT_DELETED = 'ContactDeleted';

    public const SOR_CHECK_CREATED = 'SORCheckCreated';
}
