<?php

namespace App\Http\Resources;

use App\Models\ContactAddress;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin ContactAddress */
class ContactAddressResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'address1' => $this->address1,
            'address2' => $this->address2,
            'city' => $this->city,
            'region' => $this->region,
            'postal_code' => $this->postal_code,
            'country_iso' => $this->country_iso,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
