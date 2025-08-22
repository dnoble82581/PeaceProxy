<?php

namespace App\Http\Resources;

use App\Models\Warning;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Warning */
class WarningResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'warning_type' => $this->warning_type,
            'warning' => $this->warning,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'subject_id' => $this->subject_id,
            'tenant_id' => $this->tenant_id,

            'tenant' => new TenantResource($this->whenLoaded('tenant')),
        ];
    }
}
