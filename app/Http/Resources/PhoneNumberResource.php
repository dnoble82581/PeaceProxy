<?php

namespace App\Http\Resources;

use App\Models\PhoneNumber;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin PhoneNumber */
class PhoneNumberResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'phone_number' => $this->phone_number,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'contact_id' => $this->contact_id,

            'contact' => new ContactResource($this->whenLoaded('contact')),
        ];
    }
}
