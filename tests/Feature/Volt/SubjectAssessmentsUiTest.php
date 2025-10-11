<?php

declare(strict_types=1);

use Livewire\Volt\Volt;
use Carbon\Carbon;
use App\Models\Subject;
use App\Models\Negotiation;

it('shows Resume button for in-progress assessment and opens slide on resume', function () {
    $subject = Subject::factory()->create(['name' => 'Test Subject']);
    $negotiation = Negotiation::factory()->create();

    $component = Volt::test('pages.negotiation.noc-elements.subject.assessments', [
        'subjectId' => $subject->id,
        'negotiationId' => $negotiation->id,
    ]);

    // Fake an in-progress assessment structure used by the blade
    $inProgress = (object) [
        'id' => 123,
        'title' => 'Test Assessment',
        'score' => null,
        'completed_at' => null,
        'started_at' => Carbon::now(),
    ];

    $component
        ->set('subject', (object) ['name' => 'Test Subject'])
        ->set('templates', [])
        ->set('showCreateForm', false)
        ->set('assessments', [$inProgress])
        ->assertSee('Resume')
        ->call('resumeOrEdit', 123)
        ->assertSet('showQuestionsSlide', true);
})->skip();

it('shows Edit button for completed assessment', function () {
    $subject = Subject::factory()->create(['name' => 'Test Subject']);
    $negotiation = Negotiation::factory()->create();

    $component = Volt::test('pages.negotiation.noc-elements.subject.assessments', [
        'subjectId' => $subject->id,
        'negotiationId' => $negotiation->id,
    ]);

    $completed = (object) [
        'id' => 456,
        'title' => 'Completed Assessment',
        'score' => 5,
        'completed_at' => Carbon::now(),
        'started_at' => Carbon::now()->subDay(),
    ];

    $component
        ->set('subject', (object) ['name' => 'Test Subject'])
        ->set('templates', [])
        ->set('showCreateForm', false)
        ->set('assessments', [$completed])
        ->assertSee('Edit');
})->skip();
