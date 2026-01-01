<?php

declare(strict_types=1);

use App\Models\Subject;
use Livewire\Volt\Volt;

it('updates mental health history via quick edit', function () {
    /** @var Subject $subject */
    $subject = Subject::factory()->create([
        'mental_health_history' => null,
    ]);

    Volt::test('forms.subject.edit-subject-mental-health-history', [
        'subjectId' => $subject->id,
    ])
        ->set('mental_health_history', 'Diagnosed with anxiety in 2020, CBT ongoing.')
        ->call('save')
        ->assertHasNoErrors();

    expect($subject->fresh()->mental_health_history)
        ->toBe('Diagnosed with anxiety in 2020, CBT ongoing.');
});

it('updates substance abuse history via quick edit', function () {
    /** @var Subject $subject */
    $subject = Subject::factory()->create([
        'substance_abuse_history' => null,
    ]);

    Volt::test('forms.subject.edit-subject-substance-abuse-history', [
        'subjectId' => $subject->id,
    ])
        ->set('substance_abuse_history', 'Prior alcohol misuse, attending meetings.')
        ->call('save')
        ->assertHasNoErrors();

    expect($subject->fresh()->substance_abuse_history)
        ->toBe('Prior alcohol misuse, attending meetings.');
});

it('updates criminal history via quick edit', function () {
    /** @var Subject $subject */
    $subject = Subject::factory()->create([
        'criminal_history' => null,
    ]);

    Volt::test('forms.subject.edit-subject-criminal-history', [
        'subjectId' => $subject->id,
    ])
        ->set('criminal_history', 'Misdemeanor in 2018, probation completed.')
        ->call('save')
        ->assertHasNoErrors();

    expect($subject->fresh()->criminal_history)
        ->toBe('Misdemeanor in 2018, probation completed.');
});

it('updates notes via quick edit', function () {
    /** @var Subject $subject */
    $subject = Subject::factory()->create([
        'notes' => null,
    ]);

    Volt::test('forms.subject.edit-subject-notes', [
        'subjectId' => $subject->id,
    ])
        ->set('notes', 'Coordinate with case worker on Friday.')
        ->call('save')
        ->assertHasNoErrors();

    expect($subject->fresh()->notes)
        ->toBe('Coordinate with case worker on Friday.');
});
