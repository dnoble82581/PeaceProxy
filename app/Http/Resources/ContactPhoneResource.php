<?php

namespace App\Http\Resources;

use App\Models\ContactPhone;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin ContactPhone */
class ContactPhoneResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'e164' => $this->e164,
            'ext' => $this->ext,
            'country_iso' => $this->country_iso,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
