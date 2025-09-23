<?php

namespace App\Services\Subject;

use App\DTOs\Subject\SubjectDTO;
use App\Models\Subject;
use App\Services\ContactPoint\ContactPointCreationService;
use Propaganistas\LaravelPhone\PhoneNumber;

class SubjectCreationService
{
    public function __construct(
        private ContactPointCreationService $contactPointCreationService
    ) {
    }

    public function createSubject(SubjectDTO $subjectDTO)
    {
        // Extract phone number from DTO
        $phoneNumber = $subjectDTO->phone;

        // Remove phone from the data to be saved to the subjects table
        $subjectData = $subjectDTO->toArray();
        unset($subjectData['phone']);

        // Create the subject without the phone number
        $subject = Subject::create($subjectData);

        // If a phone number is provided, create a contact point for it
        if (!empty($phoneNumber)) {
            try {
                $formattedPhone = new PhoneNumber($phoneNumber, 'US');

                // Create a ContactPoint record for the Subject with associated phone information
                $this->contactPointCreationService->createContactPoint([
                    'subject_id' => $subject->id,
                    'tenant_id' => $subject->tenant_id,
                    'kind' => 'phone',
                    'label' => 'primary',
                    'is_primary' => true,
                    'is_verified' => false,
                    'priority' => 1,
                    'e164' => $formattedPhone,
                    'ext' => null,
                    'phone_country_iso' => 'US',
                ]);
            } catch (\Exception $e) {
                // Log the error but don't fail the subject creation
            }
        }

        return $subject;
    }
}
