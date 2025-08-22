<?php

namespace App\Http\Resources;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Document */
class DocumentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'file_path' => $this->file_path,
            'file_type' => $this->file_type,
            'file_size' => $this->file_size,
            'storage_disk' => $this->storage_disk,
            'category' => $this->category,
            'description' => $this->description,
            'is_private' => $this->is_private,
            'tags' => $this->tags,
            'encrypted' => $this->encrypted,
            'access_token' => $this->access_token,
            'presigned_url_expires_at' => $this->presigned_url_expires_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'tenant_id' => $this->tenant_id,
            'negotiation_id' => $this->negotiation_id,
            'uploaded_by_id' => $this->uploaded_by_id,
            'documentable_type' => $this->documentable_type,
            'documentable_id' => $this->documentable_id,

            'tenant' => new TenantResource($this->whenLoaded('tenant')),
            'negotiation' => new NegotiationResource($this->whenLoaded('negotiation')),
            'uploaded_by' => new UserResource($this->whenLoaded('uploadedBy')),
            'documentable' => $this->whenLoaded('documentable', function () {
                switch ($this->documentable_type) {
                    case 'App\\Models\\Subject':
                        return new SubjectResource($this->documentable);
                    case 'App\\Models\\User':
                        return new UserResource($this->documentable);
                    case 'App\\Models\\Negotiation':
                        return new NegotiationResource($this->documentable);
                    default:
                        return null;
                }
            }),
        ];
    }
}
