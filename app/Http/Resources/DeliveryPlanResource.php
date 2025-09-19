<?php

namespace App\Http\Resources;

use App\Models\DeliveryPlan;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin DeliveryPlan */
class DeliveryPlanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'summary' => $this->summary,
            'category' => $this->category,
            'status' => $this->status,
            'scheduled_at' => $this->scheduled_at,
            'window_starts_at' => $this->window_starts_at,
            'window_ends_at' => $this->window_ends_at,
            'location_name' => $this->location_name,
            'location_address' => $this->location_address,
            'geo' => $this->geo,
            'route' => $this->route,
            'instructions' => $this->instructions,
            'constraints' => $this->constraints,
            'contingencies' => $this->contingencies,
            'risk_assessment' => $this->risk_assessment,
            'signals' => $this->signals,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'tenant_id' => $this->tenant_id,
            'negotiation_id' => $this->negotiation_id,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,

            'tenant' => new TenantResource($this->whenLoaded('tenant')),
            'negotiation' => new NegotiationResource($this->whenLoaded('negotiation')),
        ];
    }
}
