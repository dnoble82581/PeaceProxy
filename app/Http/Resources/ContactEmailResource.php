<?php

namespace App\Http\Resources;

use App\Models\ContactEmail;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin ContactEmail */
class ContactEmailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'contact_point_id' => $this->contact_point_id,
            'tenant_id' => $this->whenLoaded('contactPoint', function () {
                return $this->contactPoint->tenant_id;
            }),

            'tenant' => new TenantResource($this->whenLoaded('tenant')),
            'negotiation' => new NegotiationResource($this->whenLoaded('negotiation')),
        ];
    }
}
