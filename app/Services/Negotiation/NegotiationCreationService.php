<?php

namespace App\Services\Negotiation;

use App\Contracts\NegotiationRepositoryInterface;
use App\DTOs\Assessment\AssessmentDTO;
use App\Models\AssessmentTemplate;
use App\Models\AssessmentTemplateQuestion;
use App\Models\Negotiation as NegotiationModel;
use App\Models\Subject;
use App\Services\Assessment\AssessmentCreationService;
use App\Services\Map\GeocodeService;
use Carbon\Carbon;

class NegotiationCreationService
{
    public function __construct(
        protected NegotiationRepositoryInterface $negotiationRepository,
        protected GeocodeService $geocodeService,
    ) {
    }

    public function createNegotiation($data)
    {
        // Attempt to geocode from provided address fields
        $addressParts = array_filter([
            $data['location_address'] ?? null,
            $data['location_city'] ?? null,
            $data['location_state'] ?? null,
            $data['location_zip'] ?? null,
        ], fn ($v) => filled($v));

        if (! empty($addressParts)) {
            $fullAddress = implode(', ', $addressParts);
            $coords = $this->geocodeService->geocode($fullAddress);
            if ($coords) {
                $data['latitude'] = $coords['lat'];
                $data['longitude'] = $coords['lng'];
            }
        }

        /** @var NegotiationModel $negotiation */
        $negotiation = $this->negotiationRepository->createNegotiation($data);

        // Attempt to create the default FBI High Risk Assessment for this negotiation
        $this->ensureDefaultFbiAssessmentForNegotiation($negotiation);

        return $negotiation;
    }

    /**
     * Ensure the default FBI High Risk Assessment template exists and
     * create an Assessment instance for the negotiation's primary subject if present.
     */
    protected function ensureDefaultFbiAssessmentForNegotiation(NegotiationModel $negotiation): void
    {
        $tenantId = (int) $negotiation->tenant_id;

        // 1) Ensure template exists (idempotent)
        $template = AssessmentTemplate::firstOrCreate(
            [
                'tenant_id' => $tenantId,
                'name' => 'FBI High Risk Assessment',
            ],
            [
                'description' => 'Default high risk evaluation checklist used by FBI guidance.',
            ],
        );

        // 2) Ensure questions exist (idempotent)
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

        foreach ($questions as $index => $text) {
            AssessmentTemplateQuestion::firstOrCreate(
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

        // 3) If negotiation already has a primary subject, create the assessment now
        $primarySubject = $negotiation->primarySubject();
        if ($primarySubject instanceof Subject) {
            $this->createAssessmentInstance($template->id, $negotiation->id, $primarySubject->id, $tenantId);
        }
    }

    protected function createAssessmentInstance(int $templateId, int $negotiationId, int $subjectId, int $tenantId): void
    {
        $dto = new AssessmentDTO(
            id: null,
            tenant_id: $tenantId,
            assessment_template_id: $templateId,
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
