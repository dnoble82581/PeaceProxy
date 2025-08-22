<?php

namespace App\Http\Resources;

use App\Models\Call;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Call */
class CallResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'duration' => $this->duration,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'tenant_id' => $this->tenant_id,
            'subject_id' => $this->subject_id,
            'negotiation_id' => $this->negotiation_id,

            'tenant' => new TenantResource($this->whenLoaded('tenant')),
            'negotiation' => new NegotiationResource($this->whenLoaded('negotiation')),
        ];
    }
}
