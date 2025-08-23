<?php

namespace App\Http\Resources;

use App\Models\Pin;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Pin */
class PinResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'tenant_id' => $this->tenant_id,
            'user_id' => $this->user_id,

            'tenant' => new TenantResource($this->whenLoaded('tenant')),
        ];
    }
}
