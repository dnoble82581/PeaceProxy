<?php

use App\Models\Subject;
use App\Models\Image;

// This is a simple test script to verify the primaryImage method works correctly

// Create a test function
function testPrimaryImage()
{
    echo "Testing primaryImage method...\n";

    // Test case 1: Subject with no images
    $subject1 = Subject::factory()->create();
    echo "Subject with no images: " . $subject1->primaryImage() . "\n";
    // Should return the temporary URL

    // Test case 2: Subject with images but none marked as primary
    $subject2 = Subject::factory()->create();
    $image2 = Image::factory()->create([
        'imageable_id' => $subject2->id,
        'imageable_type' => Subject::class,
        'tenant_id' => $subject2->tenant_id,
        'is_primary' => false,
        'url' => 'https://example.com/test-image-1.jpg'
    ]);
    echo "Subject with non-primary image: " . $subject2->primaryImage() . "\n";
    // Should return the URL of the first image

    // Test case 3: Subject with a primary image
    $subject3 = Subject::factory()->create();
    $image3a = Image::factory()->create([
        'imageable_id' => $subject3->id,
        'imageable_type' => Subject::class,
        'tenant_id' => $subject3->tenant_id,
        'is_primary' => false,
        'url' => 'https://example.com/test-image-2.jpg'
    ]);
    $image3b = Image::factory()->create([
        'imageable_id' => $subject3->id,
        'imageable_type' => Subject::class,
        'tenant_id' => $subject3->tenant_id,
        'is_primary' => true,
        'url' => 'https://example.com/test-image-primary.jpg'
    ]);
    echo "Subject with primary image: " . $subject3->primaryImage() . "\n";
    // Should return the URL of the primary image

    echo "Test completed.\n";
}

// Run the test
testPrimaryImage();
