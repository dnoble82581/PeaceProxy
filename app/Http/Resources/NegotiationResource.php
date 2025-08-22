<?php

namespace App\Http\Resources;

use App\Models\Negotiation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Negotiation */
class NegotiationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
