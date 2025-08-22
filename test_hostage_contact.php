<?php

use App\Models\ContactEmail;
use App\Models\ContactPhone;
use App\Models\ContactPoint;
use App\Models\Hostage;
use App\Models\Negotiation;
use App\Models\Tenant;

// This script tests creating a hostage with contact points

// Get the first tenant
$tenant = Tenant::first();

// Set the current tenant
app()->forgetInstance('tenant');
app()->instance('tenant', $tenant);

// Get or create a negotiation
$negotiation = Negotiation::first() ?? Negotiation::factory()->create([
    'tenant_id' => $tenant->id,
]);

// Create a hostage
$hostage = Hostage::create([
    'tenant_id' => $tenant->id,
    'negotiation_id' => $negotiation->id,
    'name' => 'Test Hostage',
    'age' => '30',
    'gender' => 'Male',
    'created_by' => 1,
]);

// Create a phone contact point for the hostage
$phoneContactPoint = ContactPoint::create([
    'contactable_id' => $hostage->id,
    'contactable_type' => get_class($hostage),
    'tenant_id' => $tenant->id,
    'kind' => 'phone',
    'label' => 'Primary Phone',
    'is_primary' => true,
    'is_verified' => false,
    'priority' => 1,
]);

// Create a phone for the contact point
ContactPhone::create([
    'contact_point_id' => $phoneContactPoint->id,
    'e164' => '+15551234567',
    'ext' => null,
    'country_iso' => 'US',
]);

// Create an email contact point for the hostage
$emailContactPoint = ContactPoint::create([
    'contactable_id' => $hostage->id,
    'contactable_type' => get_class($hostage),
    'tenant_id' => $tenant->id,
    'kind' => 'email',
    'label' => 'Primary Email',
    'is_primary' => true,
    'is_verified' => false,
    'priority' => 1,
]);

// Create an email for the contact point
ContactEmail::create([
    'contact_point_id' => $emailContactPoint->id,
    'email' => 'test.hostage@example.com',
]);

// Test retrieving the contact points
$contactPoints = $hostage->contacts;

echo "Hostage created with ID: " . $hostage->id . "\n";
echo "Number of contact points: " . $contactPoints->count() . "\n";

foreach ($contactPoints as $contactPoint) {
    echo "Contact point kind: " . $contactPoint->kind . "\n";

    if ($contactPoint->kind === 'phone') {
        echo "Phone number: " . $contactPoint->phone->e164 . "\n";
    } elseif ($contactPoint->kind === 'email') {
        echo "Email: " . $contactPoint->email->email . "\n";
    }
}

echo "Test completed successfully!\n";
