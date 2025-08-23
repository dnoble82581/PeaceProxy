<?php

namespace App\DTOs\CallRecording;

class CallRecordingDTO
{
    public function __construct(
        public ?int $id = null,
        public ?int $call_id = null,
        public ?string $recording_sid = null,
        public ?string $status = null,
        public ?int $duration_seconds = null,
        public ?string $source = null,
        public ?string $channels = null,
        public ?string $recording_url = null,
        public ?string $storage_path = null,
        public ?float $price = null,
        public ?string $price_unit = null,
        public ?string $started_at = null,
        public ?string $ended_at = null,
        public ?array $meta = null,
        public ?string $created_at = null,
        public ?string $updated_at = null,
    ) {
    }

    /**
     * Create a DTO from an array.
     */
    public static function fromArray(array $data): CallRecordingDTO
    {
        return new self(
            id: $data['id'] ?? null,
            call_id: $data['call_id'] ?? null,
            recording_sid: $data['recording_sid'] ?? null,
            status: $data['status'] ?? null,
            duration_seconds: $data['duration_seconds'] ?? null,
            source: $data['source'] ?? null,
            channels: $data['channels'] ?? null,
            recording_url: $data['recording_url'] ?? null,
            storage_path: $data['storage_path'] ?? null,
            price: $data['price'] ?? null,
            price_unit: $data['price_unit'] ?? null,
            started_at: $data['started_at'] ?? null,
            ended_at: $data['ended_at'] ?? null,
            meta: $data['meta'] ?? null,
            created_at: $data['created_at'] ?? null,
            updated_at: $data['updated_at'] ?? null,
        );
    }

    /**
     * Convert the DTO to an array.
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'call_id' => $this->call_id,
            'recording_sid' => $this->recording_sid,
            'status' => $this->status,
            'duration_seconds' => $this->duration_seconds,
            'source' => $this->source,
            'channels' => $this->channels,
            'recording_url' => $this->recording_url,
            'storage_path' => $this->storage_path,
            'price' => $this->price,
            'price_unit' => $this->price_unit,
            'started_at' => $this->started_at,
            'ended_at' => $this->ended_at,
            'meta' => $this->meta,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
