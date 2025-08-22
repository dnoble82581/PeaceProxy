<?php

namespace App\DTOs\Subject;

use App\Enums\Subject\MoodLevels;
use App\Enums\Subject\SubjectNegotiationStatuses;
use Carbon\Carbon;

class SubjectDTO
{
    public function __construct(
        public ?int $tenant_id,

        // Basic information
        public string $name,
        public ?string $alias = null,
        public ?Carbon $date_of_birth = null,
        public ?string $gender = null,

        // Physical characteristics
        public ?string $height = null,
        public ?string $weight = null,
        public ?string $hair_color = null,
        public ?string $eye_color = null,
        public ?string $identifying_features = null,

        // Contact information
        public ?string $phone = null,
        public ?string $email = null,
        public ?string $address = null,
        public ?string $city = null,
        public ?string $state = null,
        public ?string $zip = null,
        public ?string $country = null,

        // Background information
        public ?string $occupation = null,
        public ?string $employer = null,
        public ?string $mental_health_history = null,
        public ?string $criminal_history = null,
        public ?string $substance_abuse_history = null,
        public ?string $known_weapons = null,

        // Risk assessment
        public ?array $risk_factors = null,

        // Notes and status
        public ?string $notes = null,
        public ?MoodLevels $current_mood = null,
        public ?SubjectNegotiationStatuses $status = null,
        public ?Carbon $created_at = null,
        public ?Carbon $updated_at = null,
    ) {
    }

    public static function fromArray(array $data): SubjectDTO
    {
        return new self(
            $data['id'] ?? null,
            $data['tenant_id'] ?? null,

            // Basic information
            $data['name'],
            $data['alias'] ?? null,
            isset($data['date_of_birth']) ? Carbon::parse($data['date_of_birth']) : null,
            $data['gender'] ?? null,

            // Physical characteristics
            $data['height'] ?? null,
            $data['weight'] ?? null,
            $data['hair_color'] ?? null,
            $data['eye_color'] ?? null,
            $data['identifying_features'] ?? null,

            // Contact information
            $data['phone'] ?? null,
            $data['email'] ?? null,
            $data['address'] ?? null,
            $data['city'] ?? null,
            $data['state'] ?? null,
            $data['zip'] ?? null,
            $data['country'] ?? null,

            // Background information
            $data['occupation'] ?? null,
            $data['employer'] ?? null,
            $data['mental_health_history'] ?? null,
            $data['criminal_history'] ?? null,
            $data['substance_abuse_history'] ?? null,
            $data['known_weapons'] ?? null,

            // Risk assessment
            $data['risk_factors'] ?? null,

            // Notes and status
            $data['notes'] ?? null,
            isset($data['current_mood']) ? MoodLevels::from($data['current_mood']) : null,
            isset($data['status']) ? SubjectNegotiationStatuses::from($data['status']) : null,
            isset($data['created_at']) ? Carbon::parse($data['created_at']) : null,
            isset($data['updated_at']) ? Carbon::parse($data['updated_at']) : null,
        );
    }

    public function toArray(): array
    {
        return [
            'tenant_id' => $this->tenant_id,

            // Basic information
            'name' => $this->name,
            'alias' => $this->alias,
            'date_of_birth' => $this->date_of_birth,
            'gender' => $this->gender,

            // Physical characteristics
            'height' => $this->height,
            'weight' => $this->weight,
            'hair_color' => $this->hair_color,
            'eye_color' => $this->eye_color,
            'identifying_features' => $this->identifying_features,

            // Contact information
            'phone' => $this->phone,
            'email' => $this->email,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'zip' => $this->zip,
            'country' => $this->country,

            // Background information
            'occupation' => $this->occupation,
            'employer' => $this->employer,
            'mental_health_history' => $this->mental_health_history,
            'criminal_history' => $this->criminal_history,
            'substance_abuse_history' => $this->substance_abuse_history,
            'known_weapons' => $this->known_weapons,

            // Risk assessment
            'risk_factors' => $this->risk_factors,

            // Notes and status
            'notes' => $this->notes,
            'current_mood' => $this->current_mood,
            'status' => $this->status,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
