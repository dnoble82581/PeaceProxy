<?php

namespace App\Http\Resources;

use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Log */
class LogResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'event' => $this->event,
            'channel' => $this->channel,
            'severity' => $this->severity,
            'headline' => $this->headline,
            'description' => $this->description,
            'properties' => $this->properties,
            'ipAddress' => $this->ipAddress,
            'user_agent' => $this->user_agent,
            'occured_at' => $this->occured_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'tenant_id' => $this->tenant_id,

            'tenant' => new TenantResource($this->whenLoaded('tenant')),
        ];
    }
}
