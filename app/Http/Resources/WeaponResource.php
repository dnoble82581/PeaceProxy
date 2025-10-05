<?php

namespace App\Http\Resources;

use App\Models\Weapon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Weapon */
class WeaponResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'category' => $this->category,
            'make' => $this->make,
            'model' => $this->model,
            'caliber' => $this->caliber,
            'status' => $this->status,
            'source' => $this->source,
            'threat_level' => $this->threat_level,
            'last_seen_at' => $this->last_seen_at,
            'reported_by_user_id' => $this->reported_by_user_id,
            'description' => $this->description,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'subject_id' => $this->subject_id,
            'tenant_id' => $this->tenant_id,

            'tenant' => new TenantResource($this->whenLoaded('tenant')),
        ];
    }
}
