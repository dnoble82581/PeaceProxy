<?php

declare(strict_types=1);

use App\Models\Image;
use App\Models\Subject;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Volt\Volt;

it('uploads, sets primary, and deletes subject images', function () {
    Storage::fake('s3_public');

    /** @var Subject $subject */
    $subject = Subject::factory()->create();

    $component = Volt::test('forms.subject.edit-subject-images', [
        'subjectId' => $subject->id,
    ]);

    // Upload two images
    $file1 = UploadedFile::fake()->image('photo-one.jpg', 600, 600);
    $file2 = UploadedFile::fake()->image('photo-two.jpg', 600, 600);

    $component
        ->set('newImages', [$file1, $file2])
        ->call('saveNewImages')
        ->assertHasNoErrors();

    // Assert database has 2 images for the subject
    expect(Image::query()->where('imageable_type', Subject::class)->where('imageable_id', $subject->id)->count())
        ->toBe(2);

    $images = Image::query()->where('imageable_type', Subject::class)->where('imageable_id', $subject->id)->get();

    // One should be primary
    expect($images->where('is_primary', true)->count())->toBe(1);

    // Storage files exist on fake disk
    foreach ($images as $img) {
        Storage::disk('s3_public')->assertExists($img->path);
    }

    // Set the non-primary as primary
    $nonPrimary = $images->firstWhere('is_primary', false);
    $component->call('setPrimaryImage', $nonPrimary->id);

    $images = Image::query()->where('imageable_type', Subject::class)->where('imageable_id', $subject->id)->get();
    expect($images->where('is_primary', true)->count())->toBe(1);
    expect($images->firstWhere('is_primary', true)->id)->toBe($nonPrimary->id);

    // Delete one image
    $toDelete = $images->first();
    $path = $toDelete->path;
    $component->call('deleteExistingImage', $toDelete->id);

    expect(Image::query()->whereKey($toDelete->id)->exists())->toBeFalse();
    Storage::disk('s3_public')->assertMissing($path);
});
