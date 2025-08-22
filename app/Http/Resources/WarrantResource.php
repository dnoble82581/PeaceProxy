<?php

namespace App\Http\Resources;

use App\Models\Warrant;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Warrant */
class WarrantResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'tenant_id' => $this->tenant_id,
            'subject_id' => $this->subject_id,

            'tenant' => new TenantResource($this->whenLoaded('tenant')),
        ];
    }
}
