<?php

declare(strict_types=1);

use App\DTOs\NegotiationSubject\NegotiationSubjectDTO;
use App\Enums\Subject\SubjectNegotiationRoles;
use App\Models\{Tenant, User, Negotiation, Subject, Assessment, AssessmentTemplate, AssessmentTemplateQuestion};
use App\Services\Negotiation\NegotiationCreationService;
use App\Services\NegotiationSubject\NegotiationSubjectCreationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App as AppFacade;

uses(RefreshDatabase::class);

function defaultFbiQuestions(): array
{
    return [
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
}

it('creates default FBI High Risk Assessment when a primary subject is attached to a negotiation', function () {
    $tenant = Tenant::factory()->create();
    $user = User::factory()->create(['tenant_id' => $tenant->id]);
    AppFacade::instance('currentTenant', $tenant);
    $this->actingAs($user);

    $negotiation = Negotiation::factory()->create(['tenant_id' => $tenant->id]);
    $subject = Subject::factory()->create(['tenant_id' => $tenant->id]);

    // Attach subject as primary via service (this should trigger assessment creation)
    $dto = new NegotiationSubjectDTO(
        negotiation_id: $negotiation->id,
        subject_id: $subject->id,
        role: SubjectNegotiationRoles::primary,
    );

    app(NegotiationSubjectCreationService::class)->createNegotiationSubject($dto);

    // Template exists
    $template = AssessmentTemplate::where('tenant_id', $tenant->id)
        ->where('name', 'FBI High Risk Assessment')
        ->first();
    expect($template)->not->toBeNull();

    // Questions seeded (boolean, required)
    $questions = AssessmentTemplateQuestion::where('assessment_template_id', $template->id)->get();
    expect($questions)->toHaveCount(10);
    foreach ($questions as $q) {
        expect($q->question_type)->toBe('boolean');
        expect((bool) $q->is_required)->toBeTrue();
    }

    // Assessment created
    $assessment = Assessment::where('tenant_id', $tenant->id)
        ->where('negotiation_id', $negotiation->id)
        ->where('subject_id', $subject->id)
        ->where('assessment_template_id', $template->id)
        ->first();
    expect($assessment)->not->toBeNull();
    expect($assessment->title)->toBe('FBI High Risk Assessment');
});

it('ensures default template and questions are idempotent and creates assessment on negotiation creation if primary subject present', function () {
    $tenant = Tenant::factory()->create();
    $user = User::factory()->create(['tenant_id' => $tenant->id]);
    AppFacade::instance('currentTenant', $tenant);
    $this->actingAs($user);

    $subject = Subject::factory()->create(['tenant_id' => $tenant->id]);

    // Create negotiation via service
    /** @var NegotiationCreationService $service */
    $service = app(NegotiationCreationService::class);

    // First, create negotiation without subjects
    $negotiation = $service->createNegotiation([
        'tenant_id' => $tenant->id,
        'title' => 'Test Negotiation',
        'status' => \App\Enums\Negotiation\NegotiationStatuses::active->value,
        'type' => \App\Enums\Negotiation\NegotiationTypes::hostage->value,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // Attach primary subject afterwards -> should create assessment now
    $dto = new NegotiationSubjectDTO(
        negotiation_id: $negotiation->id,
        subject_id: $subject->id,
        role: SubjectNegotiationRoles::primary,
    );
    app(NegotiationSubjectCreationService::class)->createNegotiationSubject($dto);

    $template = AssessmentTemplate::where('tenant_id', $tenant->id)
        ->where('name', 'FBI High Risk Assessment')
        ->first();

    // Run idempotency by "touching" creation again (no duplicates expected)
    app(NegotiationSubjectCreationService::class)->createNegotiationSubject(new NegotiationSubjectDTO(
        negotiation_id: $negotiation->id,
        subject_id: $subject->id,
        role: SubjectNegotiationRoles::primary,
    ));

    $questions = AssessmentTemplateQuestion::where('assessment_template_id', $template->id)->get();
    expect($questions)->toHaveCount(10);

    $assessments = Assessment::where('tenant_id', $tenant->id)
        ->where('negotiation_id', $negotiation->id)
        ->where('subject_id', $subject->id)
        ->where('assessment_template_id', $template->id)
        ->get();

    // Only one assessment should exist
    expect($assessments)->toHaveCount(1);
});
