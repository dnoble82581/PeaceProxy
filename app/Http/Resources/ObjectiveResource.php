<?php

namespace App\Http\Resources;

use App\Models\Objective;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Objective */
class ObjectiveResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'tenant_id' => $this->tenant_id,
            'negotiation_id' => $this->negotiation_id,
            'created_by_id' => $this->created_by_id,

            'tenant' => new TenantResource($this->whenLoaded('tenant')),
            'negotiation' => new NegotiationResource($this->whenLoaded('negotiation')),
        ];
    }
}
