<?php

namespace App\Http\Resources;

use App\Models\Email;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Email */
class EmailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'contact_id' => $this->contact_id,

            'contact' => new ContactResource($this->whenLoaded('contact')),
        ];
    }
}
