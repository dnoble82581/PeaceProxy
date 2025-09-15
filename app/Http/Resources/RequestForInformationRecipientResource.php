<?php

namespace App\Http\Resources;

use App\Models\RequestForInformationRecipient;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin RequestForInformationRecipient */
class RequestForInformationRecipientResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'is_read' => $this->is_read,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'request_for_information_id' => $this->request_for_information_id,
            'tenant_id' => $this->tenant_id,
            'user_id' => $this->user_id,

            'requestForInformation' => new RequestForInformationResource($this->whenLoaded('requestForInformation')),
            'tenant' => new TenantResource($this->whenLoaded('tenant')),
        ];
    }
}
