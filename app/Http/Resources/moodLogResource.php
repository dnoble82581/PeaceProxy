<?php

namespace App\Http\Resources;

use App\Models\moodLog;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin moodLog */
class moodLogResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'mood' => $this->mood,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
