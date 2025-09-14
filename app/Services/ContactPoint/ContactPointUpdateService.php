<?php

namespace App\Services\ContactPoint;

use App\Models\ContactPoint;
use App\Models\ContactEmail;
use App\Models\ContactPhone;
use App\Models\ContactAddress;
use Illuminate\Support\Facades\DB;

class ContactPointUpdateService
{
    private function addLogEntry(ContactPoint $contactPoint): void
    {
        $user = auth()->user();

        app(\App\Services\Log\LogService::class)->writeAsync(
            tenantId: tenant()->id,
            event: 'contactpoint.updated',
            headline: "{$user->name} updated a contact point",
            about: $contactPoint,      // loggable target
            by: $user,                 // actor
            description: "Contact point updated: {$contactPoint->kind} - {$contactPoint->label}",
            properties: [
                'contactable_type' => $contactPoint->contactable_type,
                'contactable_id' => $contactPoint->contactable_id,
                'kind' => $contactPoint->kind,
                'is_primary' => $contactPoint->is_primary,
            ],
        );
    }
    public function updateContactPoint(int $id, array $data): ContactPoint
    {
        $contactPoint = DB::transaction(function () use ($id, $data) {
            // Get the contact point
            $contactPoint = ContactPoint::findOrFail($id);

            // Update the contact point
            $contactPoint->update([
                'kind' => $data['kind'],
                'label' => $data['label'],
                'is_primary' => $data['is_primary'],
                'is_verified' => $data['is_verified'],
                'priority' => $data['priority'],
            ]);

            // Update or create the specific contact point type
            if ($data['kind'] === 'email') {
                // Delete other types if they exist
                $contactPoint->phone()->delete();
                $contactPoint->address()->delete();

                // Update or create email
                ContactEmail::updateOrCreate(
                    ['contact_point_id' => $contactPoint->id],
                    ['email' => $data['email']]
                );
            } elseif ($data['kind'] === 'phone') {
                // Delete other types if they exist
                $contactPoint->email()->delete();
                $contactPoint->address()->delete();

                // Update or create phone
                ContactPhone::updateOrCreate(
                    ['contact_point_id' => $contactPoint->id],
                    [
                        'e164' => $data['e164'],
                        'ext' => $data['ext'],
                        'country_iso' => $data['phone_country_iso'],
                    ]
                );
            } elseif ($data['kind'] === 'address') {
                // Delete other types if they exist
                $contactPoint->email()->delete();
                $contactPoint->phone()->delete();

                // Update or create address
                ContactAddress::updateOrCreate(
                    ['contact_point_id' => $contactPoint->id],
                    [
                        'address1' => $data['address1'],
                        'address2' => $data['address2'],
                        'city' => $data['city'],
                        'region' => $data['region'],
                        'postal_code' => $data['postal_code'],
                        'country_iso' => $data['address_country_iso'],
                        'latitude' => $data['latitude'],
                        'longitude' => $data['longitude'],
                    ]
                );
            }

            return $contactPoint->fresh(['email', 'phone', 'address']);
        });

        // Log the update
        $this->addLogEntry($contactPoint);

        return $contactPoint;
    }
}
