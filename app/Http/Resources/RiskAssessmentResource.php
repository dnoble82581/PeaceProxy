<?php

namespace App\Http\Resources;

use App\Models\RiskAssessment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin RiskAssessment */
class RiskAssessmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'tenant_id' => $this->tenant_id,
            'subject_id' => $this->subject_id,

            'tenant' => new TenantResource($this->whenLoaded('tenant')),
        ];
    }
}
