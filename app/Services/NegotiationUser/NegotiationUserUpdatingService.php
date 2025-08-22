<?php

namespace App\Services\NegotiationUser;

use Illuminate\Support\Facades\DB;

class NegotiationUserUpdatingService
{
    public function __construct()
    {
    }

    public function updateLeftAtForUser(int $negotiationId, string $role)
    {
        DB::table('negotiation_users')
            ->where('negotiation_id', $negotiationId)
            ->where('user_id', auth()->id())
            ->where('role', $role)
            ->where('left_at', null)
            ->update([
                'left_at' => now(),
                'updated_at' => now(),
            ]);
    }
}
