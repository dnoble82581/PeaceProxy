<?php

namespace App\Services\ContactPoint;

use App\Models\ContactPoint;

class ContactPointDeletionService
{
    private function addLogEntry(ContactPoint $contactPoint): void
    {
        $user = auth()->user();

        app(\App\Services\Log\LogService::class)->writeAsync(
            tenantId: tenant()->id,
            event: 'contactpoint.deleted',
            headline: "{$user->name} deleted a contact point",
            about: $contactPoint,      // loggable target
            by: $user,                 // actor
            description: "Contact point deleted: {$contactPoint->kind} - {$contactPoint->label}",
            properties: [
                'contactable_type' => $contactPoint->contactable_type,
                'contactable_id' => $contactPoint->contactable_id,
                'kind' => $contactPoint->kind,
                'is_primary' => $contactPoint->is_primary,
            ],
        );
    }
    public function deleteContactPoint(int $id): bool
    {
        $contactPoint = ContactPoint::findOrFail($id);

        // Log the deletion
        $this->addLogEntry($contactPoint);

        // Delete the specific contact point type (email, phone, address)
        if ($contactPoint->kind === 'email') {
            $contactPoint->email()->delete();
        } elseif ($contactPoint->kind === 'phone') {
            $contactPoint->phone()->delete();
        } elseif ($contactPoint->kind === 'address') {
            $contactPoint->address()->delete();
        }

        // Delete the contact point itself
        return $contactPoint->delete();
    }
}
