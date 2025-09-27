<?php

namespace App\Services\ContactPoint;

use App\Events\Subject\ContactDeletedEvent;
use App\Models\ContactPoint;

class ContactPointDeletionService
{
    public function deleteContactPoint(int $id): bool
    {
        $contactPoint = ContactPoint::findOrFail($id);

        // Delete the specific contact point type (email, phone, address)
        if ($contactPoint->kind === 'email') {
            $contactPoint->email()->delete();
        } elseif ($contactPoint->kind === 'phone') {
            $contactPoint->phone()->delete();
        } elseif ($contactPoint->kind === 'address') {
            $contactPoint->address()->delete();
        }

        $data = [
            'contactPointId' => $contactPoint->id,
            'subjectId' => $contactPoint->contactable_id,
        ];

        // Delete the contact point itself
        $contactDeleted = $contactPoint->delete();

        if ($contactDeleted) {
            event(new ContactDeletedEvent($data));
        }

        return $contactDeleted;
    }
}
