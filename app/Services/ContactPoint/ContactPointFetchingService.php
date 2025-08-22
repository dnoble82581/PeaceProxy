<?php

namespace App\Services\ContactPoint;

use App\Models\ContactPoint;
use App\Models\Subject;

class ContactPointFetchingService
{
    public function getContactPointById(int $id): ContactPoint
    {
        $contactPoint = ContactPoint::findOrFail($id);

        // Only load the relationship that matches the contact point's kind
        if ($contactPoint->kind === 'email') {
            $contactPoint->load('email');
        } elseif ($contactPoint->kind === 'phone') {
            $contactPoint->load('phone');
        } elseif ($contactPoint->kind === 'address') {
            $contactPoint->load('address');
        }

        return $contactPoint;
    }

    public function getContactPointsBySubject(Subject $subject)
    {
        $contactPoints = $subject->contactPoints()->get();
        $this->loadRelationshipsSelectively($contactPoints);
        return $contactPoints;
    }

    public function getContactPointsBySubjectId(int $subjectId)
    {
        $contactPoints = ContactPoint::where('contactable_id', $subjectId)
            ->where('contactable_type', Subject::class)
            ->get();
        $this->loadRelationshipsSelectively($contactPoints);
        return $contactPoints;
    }

    /**
     * Load only the necessary relationships for each contact point based on its kind
     */
    private function loadRelationshipsSelectively($contactPoints)
    {
        // For each contact point, load only the relationship that matches its kind
        foreach ($contactPoints as $contactPoint) {
            if ($contactPoint->kind === 'email') {
                $contactPoint->load('email');
            } elseif ($contactPoint->kind === 'phone') {
                $contactPoint->load('phone');
            } elseif ($contactPoint->kind === 'address') {
                $contactPoint->load('address');
            }
        }
    }
}
