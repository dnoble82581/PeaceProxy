<?php

namespace App\Http\Resources;

use App\Models\AssessmentTemplate;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin AssessmentTemplate */
class AssessmentTemplateResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'tenant_id' => $this->tenant_id,

            'tenant' => new TenantResource($this->whenLoaded('tenant')),
        ];
    }
}
