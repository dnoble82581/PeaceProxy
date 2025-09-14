<?php

namespace App\Services\ContactPoint;

use App\Models\ContactAddress;
use App\Models\ContactEmail;
use App\Models\ContactPhone;
use App\Models\ContactPoint;
use Illuminate\Support\Facades\DB;

class ContactPointCreationService
{
    /**
     * Create a new contact point
     *
     * This method accepts an array of data with the following keys:
     * - contactable_id: The ID of the model this contact point belongs to
     * - contactable_type: The class name of the model this contact point belongs to
     * - OR -
     * - subject_id: The ID of the subject this contact point belongs to (will be mapped to contactable_id)
     * - tenant_id: The ID of the tenant this contact point belongs to
     * - kind: The kind of contact point (email, phone, address)
     * - label: The label for this contact point (home, work, etc.)
     * - is_primary: Whether this is the primary contact point
     * - is_verified: Whether this contact point has been verified
     * - priority: The priority of this contact point
     *
     * Additional fields are required based on the 'kind' value:
     * - For 'email': email
     * - For 'phone': e164, ext, phone_country_iso
     * - For 'address': address1, address2, city, region, postal_code, address_country_iso, latitude, longitude
     *
     * @param  array  $data  The data for the contact point
     * @return ContactPoint The created contact point
     *
     * @throws \Throwable
     */
    public function createContactPoint(array $data): ContactPoint
    {
        $contactPoint = DB::transaction(function () use ($data) {
            // Handle the case where subject_id is provided instead of contactable_id
            $contactableId = $data['contactable_id'] ?? null;
            $contactableType = $data['contactable_type'] ?? null;

            // If contactable_id is not provided but subject_id is, use subject_id as contactable_id
            // and set contactable_type to Subject model
            if ((! $contactableId || ! $contactableType) && isset($data['subject_id'])) {
                $contactableId = $data['subject_id'];
                $contactableType = 'App\\Models\\Subject';
            }

            // Create the contact point
            $contactPoint = ContactPoint::create([
                'contactable_id' => $contactableId,
                'contactable_type' => $contactableType,
                'tenant_id' => $data['tenant_id'],
                'kind' => $data['kind'],
                'label' => $data['label'],
                'is_primary' => $data['is_primary'],
                'is_verified' => $data['is_verified'],
                'priority' => $data['priority'],
            ]);

            // Create the specific contact point type
            if ($data['kind'] === 'email') {
                ContactEmail::create([
                    'contact_point_id' => $contactPoint->id,
                    'email' => $data['email'],
                ]);
            } elseif ($data['kind'] === 'phone') {
                ContactPhone::create([
                    'contact_point_id' => $contactPoint->id,
                    'e164' => $data['e164'],
                    'ext' => $data['ext'],
                    'country_iso' => $data['phone_country_iso'],
                ]);
            } elseif ($data['kind'] === 'address') {
                ContactAddress::create([
                    'contact_point_id' => $contactPoint->id,
                    'address1' => $data['address1'],
                    'address2' => $data['address2'],
                    'city' => $data['city'],
                    'region' => $data['region'],
                    'postal_code' => $data['postal_code'],
                    'country_iso' => $data['address_country_iso'],
                    'latitude' => $data['latitude'],
                    'longitude' => $data['longitude'],
                ]);
            }

            return $contactPoint;
        });

        $log = $this->addLogEntry($contactPoint);
        logger($log);

        return $contactPoint;
    }

    private function addLogEntry(ContactPoint $contactPoint)
    {
        $user = auth()->user();

        return app(\App\Services\Log\LogService::class)->write(
            tenantId: tenant()->id,
            event: 'contactpoint.created',
            headline: "{$user->name} created a contact point",
            about: $contactPoint,      // loggable target
            by: $user,                 // actor
            description: "Contact point created: {$contactPoint->kind} - {$contactPoint->label}",
            properties: [
                'contactable_type' => $contactPoint->contactable_type,
                'contactable_id' => $contactPoint->contactable_id,
                'kind' => $contactPoint->kind,
                'is_primary' => $contactPoint->is_primary,
            ],
        );
    }
}
