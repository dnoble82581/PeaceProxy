<?php

use App\Models\Hostage;
use App\Models\Image;
use App\Models\Negotiation;
use App\Models\Tenant;

// This is a test script to verify the avatarUrl method works correctly for Hostage model

// Create a test function
function testHostageAvatarUrl()
{
    echo "Testing Hostage avatarUrl method...\n";

    // Create a tenant for testing
    $tenant = Tenant::factory()->create();

    // Create a negotiation for the hostages
    $negotiation = Negotiation::factory()->create([
        'tenant_id' => $tenant->id,
    ]);

    // Test case 1: Hostage with no images
    $hostage1 = Hostage::factory()->create([
        'name' => 'John Doe',
        'tenant_id' => $tenant->id,
        'negotiation_id' => $negotiation->id,
    ]);

    echo "Hostage with no images:\n";
    echo "Name: " . $hostage1->name . "\n";
    echo "Initials: " . $hostage1->initials() . "\n";
    echo "Avatar URL: " . $hostage1->avatarUrl() . "\n";
    // Should return a URL with the hostage's initials

    // Test case 2: Hostage with images but none marked as primary
    $hostage2 = Hostage::factory()->create([
        'name' => 'Jane Smith',
        'tenant_id' => $tenant->id,
        'negotiation_id' => $negotiation->id,
    ]);

    $image2 = Image::factory()->create([
        'imageable_id' => $hostage2->id,
        'imageable_type' => Hostage::class,
        'tenant_id' => $tenant->id,
        'is_primary' => false,
        'url' => 'https://example.com/test-hostage-image-1.jpg'
    ]);

    echo "\nHostage with non-primary image:\n";
    echo "Name: " . $hostage2->name . "\n";
    echo "Avatar URL: " . $hostage2->avatarUrl() . "\n";
    // Should return the URL of the first image

    // Test case 3: Hostage with a primary image
    $hostage3 = Hostage::factory()->create([
        'name' => 'Robert Johnson',
        'tenant_id' => $tenant->id,
        'negotiation_id' => $negotiation->id,
    ]);

    $image3a = Image::factory()->create([
        'imageable_id' => $hostage3->id,
        'imageable_type' => Hostage::class,
        'tenant_id' => $tenant->id,
        'is_primary' => false,
        'url' => 'https://example.com/test-hostage-image-2.jpg'
    ]);

    $image3b = Image::factory()->create([
        'imageable_id' => $hostage3->id,
        'imageable_type' => Hostage::class,
        'tenant_id' => $tenant->id,
        'is_primary' => true,
        'url' => 'https://example.com/test-hostage-image-primary.jpg'
    ]);

    echo "\nHostage with primary image:\n";
    echo "Name: " . $hostage3->name . "\n";
    echo "Avatar URL: " . $hostage3->avatarUrl() . "\n";
    // Should return the URL of the primary image

    echo "\nTest completed.\n";
}

// Run the test
testHostageAvatarUrl();
