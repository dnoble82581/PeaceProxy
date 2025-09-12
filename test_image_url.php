<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\Image;
use App\Models\Subject;
use Illuminate\Support\Facades\Storage;

// This script tests the Image model's url() method to ensure it works correctly
// in different environment configurations.

echo "Testing Image URL Generation\n";
echo "===========================\n\n";

// Get the first subject with images
$subject = Subject::with('images')->whereHas('images')->first();

if (!$subject) {
    echo "No subjects with images found. Please create a subject with images first.\n";
    exit;
}

echo "Subject: {$subject->name}\n";
echo "Number of images: " . $subject->images->count() . "\n\n";

// Test each image
foreach ($subject->images as $index => $image) {
    $imageNumber = $index + 1;
    echo "Image #{$imageNumber}:\n";
    echo "  Path: {$image->path}\n";

    // Check if url property exists
    echo "  URL property: " . (isset($image->url) && !empty($image->url) ? $image->url : 'Not set') . "\n";

    // Get URL using url() method
    $generatedUrl = $image->url();
    echo "  Generated URL: {$generatedUrl}\n";

    // Check if file exists in storage
    $disk = $image->disk ?? config('filesystems.default', 'local');
    $exists = Storage::disk($disk)->exists($image->path);
    echo "  File exists in '{$disk}' disk: " . ($exists ? 'Yes' : 'No') . "\n";

    // Check if URL is accessible
    $headers = @get_headers($generatedUrl);
    $accessible = $headers && strpos($headers[0], '200') !== false;
    echo "  URL is accessible: " . ($accessible ? 'Yes' : 'No') . "\n\n";
}

echo "Test completed.\n";
