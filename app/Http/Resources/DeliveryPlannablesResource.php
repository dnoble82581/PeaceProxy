<?php

namespace App\Http\Resources;

use App\Models\DeliveryPlannables;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin DeliveryPlannables */
class DeliveryPlannablesResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'plannable' => $this->plannable,
            'role' => $this->role,
            'notes' => $this->notes,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'delivery_plan_id' => $this->delivery_plan_id,

            'deliveryPlan' => new DeliveryPlanResource($this->whenLoaded('deliveryPlan')),
        ];
    }
}
