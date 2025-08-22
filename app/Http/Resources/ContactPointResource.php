<?php

namespace App\Http\Resources;

use App\Models\ContactPoint;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin ContactPoint */
class ContactPointResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'kind' => $this->kind,
            'label' => $this->label,
            'is_primary' => $this->is_primary,
            'is_verified' => $this->is_verified,
            'verified_at' => $this->verified_at,
            'priority' => $this->priority,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'contactable_id' => $this->contactable_id,
            'contactable_type' => $this->contactable_type,
            'tenant_id' => $this->tenant_id,

            'tenant' => new TenantResource($this->whenLoaded('tenant')),
            'negotiation' => new NegotiationResource($this->whenLoaded('negotiation')),
        ];
    }
}
