<?php

declare(strict_types=1);

use App\Models\Subject;
use Livewire\Volt\Volt;

it('updates basic subject info via quick edit', function () {
    /** @var Subject $subject */
    $subject = Subject::factory()->create([
        'name' => 'Original Name',
    ]);

    $component = Volt::test('forms.subject.edit-subject-basic', [
        'subjectId' => $subject->id,
    ]);

    $component->set('name', 'Updated Name')->call('save')->assertHasNoErrors();

    expect($subject->fresh()->name)->toBe('Updated Name');
});

it('updates subject aliases via quick edit', function () {
    /** @var Subject $subject */
    $subject = Subject::factory()->create([
        'alias' => ['Smithy'],
    ]);

    $component = Volt::test('forms.subject.edit-subject-aliases', [
        'subjectId' => $subject->id,
    ]);

    $component->set('aliasesText', "Alpha, Beta, Beta\nGamma")->call('save')->assertHasNoErrors();

    expect($subject->fresh()->alias)->toBe(['Alpha', 'Beta', 'Gamma']);
});

it('updates subject risks via quick edit', function () {
    /** @var Subject $subject */
    $subject = Subject::factory()->create([
        'risk_factors' => ['Armed'],
    ]);

    $component = Volt::test('forms.subject.edit-subject-risks', [
        'subjectId' => $subject->id,
    ]);

    $component->set('risksText', "Armed\nSuicidal\nArmed")->call('save')->assertHasNoErrors();

    expect($subject->fresh()->risk_factors)->toBe(['Armed', 'Suicidal']);
});
