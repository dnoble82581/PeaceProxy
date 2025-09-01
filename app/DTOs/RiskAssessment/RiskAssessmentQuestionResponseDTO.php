<?php

namespace App\DTOs\RiskAssessment;

use Carbon\Carbon;

class RiskAssessmentQuestionResponseDTO
{
    public function __construct(
        public ?int $id = null,
        public ?int $question_id = null,
        public ?int $tenant_id = null,
        public ?int $created_by_id = null,
        public ?string $text_response = null,
        public ?int $number_response = null,
        public ?string $rating_string_response = null,
        public ?string $rating_number_response = null,
        public ?string $textarea_response = null,
        public ?string $select_response = null,
        public ?array $multiselect_response = null,
        public ?array $checkbox_response = null,
        public ?string $radio_response = null,
        public ?Carbon $date_response = null,
        public ?Carbon $time_response = null,
        public ?Carbon $datetime_response = null,
        public ?string $file_response = null,
        public ?Carbon $created_at = null,
        public ?Carbon $updated_at = null,
    ) {
    }

    /**
     * Create a DTO from an array.
     */
    public static function fromArray(array $data): RiskAssessmentQuestionResponseDTO
    {
        return new self(
            $data['id'] ?? null,
            $data['question_id'] ?? null,
            $data['tenant_id'] ?? null,
            $data['created_by_id'] ?? null,
            $data['text_response'] ?? null,
            $data['number_response'] ?? null,
            $data['rating_string_response'] ?? null,
            $data['rating_number_response'] ?? null,
            $data['textarea_response'] ?? null,
            $data['select_response'] ?? null,
            $data['multiselect_response'] ?? null,
            $data['checkbox_response'] ?? null,
            $data['radio_response'] ?? null,
            isset($data['date_response']) ? Carbon::parse($data['date_response']) : null,
            isset($data['time_response']) ? Carbon::parse($data['time_response']) : null,
            isset($data['datetime_response']) ? Carbon::parse($data['datetime_response']) : null,
            $data['file_response'] ?? null,
            isset($data['created_at']) ? Carbon::parse($data['created_at']) : null,
            isset($data['updated_at']) ? Carbon::parse($data['updated_at']) : null,
        );
    }

    /**
     * Convert the DTO to an array.
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'question_id' => $this->question_id,
            'tenant_id' => $this->tenant_id,
            'created_by_id' => $this->created_by_id,
            'text_response' => $this->text_response,
            'number_response' => $this->number_response,
            'rating_string_response' => $this->rating_string_response,
            'rating_number_response' => $this->rating_number_response,
            'textarea_response' => $this->textarea_response,
            'select_response' => $this->select_response,
            'multiselect_response' => $this->multiselect_response,
            'checkbox_response' => $this->checkbox_response,
            'radio_response' => $this->radio_response,
            'date_response' => $this->date_response,
            'time_response' => $this->time_response,
            'datetime_response' => $this->datetime_response,
            'file_response' => $this->file_response,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
