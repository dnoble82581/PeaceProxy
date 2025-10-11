<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RedirectToNocMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        $assignment = $user->activeIncidentAssignment;

        if ($assignment) {
            return match ($assignment->team?->slug ?? $assignment->role) {
                'tactical_commander', 'tactical_user' => redirect()->to(route('negotiation.tactical-noc')),
                default => redirect()->to(route('negotiation-noc', $assignment->negotiation)),
            };
        }

        // 2) Default by primary team
        $teamSlug = $user->primaryTeam()?->slug;

        return match ($teamSlug) {
            'tactical' => redirect()->to(route('negotiation.tactical-noc')),
            'negotiation' => redirect()->to(route('negotiation-noc')),
            default => redirect()->to('/'),
        };
    }
}
