<?php

namespace App\Services\NegotiationSubject;

use App\DTOs\Assessment\AssessmentDTO;
use App\DTOs\NegotiationSubject\NegotiationSubjectDTO;
use App\Enums\Subject\SubjectNegotiationRoles;
use App\Models\Assessment;
use App\Models\AssessmentTemplate;
use App\Models\NegotiationSubject;
use App\Services\Assessment\AssessmentCreationService;
use Carbon\Carbon;

class NegotiationSubjectCreationService
{
    public function __construct()
    {
    }

    public function createNegotiationSubject(NegotiationSubjectDTO $negotiationSubjectDTO)
    {
        $created = NegotiationSubject::create($negotiationSubjectDTO->toArray());

        // If this subject is marked as primary, ensure the default FBI assessment exists for this negotiation/subject
        if ($negotiationSubjectDTO->role === SubjectNegotiationRoles::primary) {
            $this->ensureDefaultFbiAssessmentForNegotiationSubject(
                negotiationId: $created->negotiation_id,
                subjectId: $created->subject_id,
            );
        }
    }

    protected function ensureDefaultFbiAssessmentForNegotiationSubject(int $negotiationId, int $subjectId): void
    {
        $tenantId = tenant()->id;

        // 1) Ensure template exists
        $template = AssessmentTemplate::firstOrCreate(
            [
                'tenant_id' => $tenantId,
                'name' => 'FBI High Risk Assessment',
            ],
            [
                'description' => 'Default high risk evaluation checklist used by FBI guidance.',
            ],
        );

        // 2) Ensure questions exist
        $questions = [
            'Subject deliberately commits an action they know will cause a response from and confrontations with police.',
            'Victim is known and specifically selected by the subject, especially if the subject has had romantic involvement with the victim or the victim is a family member.',
            'There is a history of problems between the subject and the victim severe enough to warrant police intervention, especially if problems involve allegations of domestic or child abuse and/or the victim has filed a complaint against the subject.',
            'Subject has made direct threats against (or actual injury to) the victim, combined with no substantive demand.',
            'There is a history of previous similar incidents.',
            'Subject has experienced multiple recent life stressors.',
            'Cultural background of subject emphasizes significance of “loss of face” and/or importance of macho/male dominance in relationships.',
            'Subject lacks familial or social support system.',
            'There is a verbalization of intent to commit suicide.',
            'Subject has given a “verbal will” to someone or has set their affairs in order.12',
        ];

        foreach ($questions as $text) {
            \App\Models\AssessmentTemplateQuestion::firstOrCreate(
                [
                    'assessment_template_id' => $template->id,
                    'question' => $text,
                ],
                [
                    'question_type' => 'boolean',
                    'question_category' => 'FBI High Risk',
                    'options' => [],
                    'is_required' => true,
                ],
            );
        }

        // 3) Only create the assessment if one doesn't already exist for this negotiation/subject/template
        $exists = Assessment::query()
            ->where('tenant_id', $tenantId)
            ->where('negotiation_id', $negotiationId)
            ->where('subject_id', $subjectId)
            ->where('assessment_template_id', $template->id)
            ->exists();

        if (! $exists) {
            $dto = new AssessmentDTO(
                id: null,
                tenant_id: $tenantId,
                assessment_template_id: $template->id,
                negotiation_id: $negotiationId,
                subject_id: $subjectId,
                started_at: Carbon::now(),
                completed_at: null,
                title: 'FBI High Risk Assessment',
                created_at: Carbon::now(),
                updated_at: Carbon::now(),
            );

            app(AssessmentCreationService::class)->createAssessment($dto);
        }
    }
}
