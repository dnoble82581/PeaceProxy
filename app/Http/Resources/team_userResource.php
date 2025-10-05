<?php

namespace App\Http\Resources;

use App\Models\team_user;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin team_user */
class team_userResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'is_primary' => $this->is_primary,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'team_id' => $this->team_id,
            'user_id' => $this->user_id,

            'team' => new TeamResource($this->whenLoaded('team')),
        ];
    }
}
