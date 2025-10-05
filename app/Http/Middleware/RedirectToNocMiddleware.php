<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RedirectToNocMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        // 1) Active assignment overrides
        $assignment = $user->activeIncidentAssignment;
        //    case TeamCoordinator = 'team_coordinator';
        //    case TeamLeader = 'team_leader';
        //    case PrimaryNegotiator = 'primary_negotiator';
        //    case SecondaryNegotiator = 'secondary_negotiator';
        //    case Recorder = 'recorder';
        //    case IntelligenceCoordinator = 'intelligence_coordinator';
        //    case MentalHealthCoordinator = 'mental_health_coordinator';
        //    case TacticalCommander = 'tactical_commander';
        //    case TacticalUser = 'tactical_user';
        //    case Administration = 'administration';

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
