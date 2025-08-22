<?php

use App\Models\Subject;
use App\Models\Contact;
use App\Models\Email;
use App\Models\Address;

// Test creating a subject with a phone contact
$subjectWithPhone = Subject::factory()
    ->withPhoneContact()
    ->create();

echo "Created subject with phone contact: " . $subjectWithPhone->id . "\n";
echo "Contact count: " . $subjectWithPhone->contacts()->count() . "\n";
echo "Phone contact: " . $subjectWithPhone->contacts()->where('type', 'phone')->first()->id . "\n";
echo "Phone number: " . $subjectWithPhone->contacts()->where('type', 'phone')->first()->phoneNumbers()->first()->phone_number . "\n\n";

// Test creating a subject with an email contact
$subjectWithEmail = Subject::factory()
    ->withEmailContact()
    ->create();

echo "Created subject with email contact: " . $subjectWithEmail->id . "\n";
echo "Contact count: " . $subjectWithEmail->contacts()->count() . "\n";
echo "Email contact: " . $subjectWithEmail->contacts()->where('type', 'email')->first()->id . "\n";
echo "Email: " . $subjectWithEmail->contacts()->where('type', 'email')->first()->emails()->first()->email . "\n\n";

// Test creating a subject with an address contact
$subjectWithAddress = Subject::factory()
    ->withAddressContact()
    ->create();

echo "Created subject with address contact: " . $subjectWithAddress->id . "\n";
echo "Contact count: " . $subjectWithAddress->contacts()->count() . "\n";
echo "Address contact: " . $subjectWithAddress->contacts()->where('type', 'address')->first()->id . "\n";
echo "Address: " . $subjectWithAddress->contacts()->where('type', 'address')->first()->addresses()->first()->street . ", " .
    $subjectWithAddress->contacts()->where('type', 'address')->first()->addresses()->first()->city . ", " .
    $subjectWithAddress->contacts()->where('type', 'address')->first()->addresses()->first()->state . " " .
    $subjectWithAddress->contacts()->where('type', 'address')->first()->addresses()->first()->zip . "\n\n";

// Test creating a subject with all contacts
$subjectWithAllContacts = Subject::factory()
    ->withAllContacts()
    ->create();

echo "Created subject with all contacts: " . $subjectWithAllContacts->id . "\n";
echo "Contact count: " . $subjectWithAllContacts->contacts()->count() . "\n";
echo "Phone contact: " . $subjectWithAllContacts->contacts()->where('type', 'phone')->first()->id . "\n";
echo "Phone number: " . $subjectWithAllContacts->contacts()->where('type', 'phone')->first()->phoneNumbers()->first()->phone_number . "\n";
echo "Email contact: " . $subjectWithAllContacts->contacts()->where('type', 'email')->first()->id . "\n";
echo "Email: " . $subjectWithAllContacts->contacts()->where('type', 'email')->first()->emails()->first()->email . "\n";
echo "Address contact: " . $subjectWithAllContacts->contacts()->where('type', 'address')->first()->id . "\n";
echo "Address: " . $subjectWithAllContacts->contacts()->where('type', 'address')->first()->addresses()->first()->street . ", " .
    $subjectWithAllContacts->contacts()->where('type', 'address')->first()->addresses()->first()->city . ", " .
    $subjectWithAllContacts->contacts()->where('type', 'address')->first()->addresses()->first()->state . " " .
    $subjectWithAllContacts->contacts()->where('type', 'address')->first()->addresses()->first()->zip . "\n";
