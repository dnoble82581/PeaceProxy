<?php

namespace App\Services\ContactPoint;

use App\Models\ContactPoint;
use App\Models\ContactEmail;
use App\Models\ContactPhone;
use App\Models\ContactAddress;
use Illuminate\Support\Facades\DB;

class ContactPointCreationService
{
    public function createContactPoint(array $data): ContactPoint
    {
        return DB::transaction(function () use ($data) {
            // Create the contact point
            $contactPoint = ContactPoint::create([
                'contactable_id' => $data['contactable_id'],
                'contactable_type' => $data['contactable_type'],
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
    }
}
