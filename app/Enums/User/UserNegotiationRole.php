<?php

namespace App\Enums\User;

enum UserNegotiationRole: string
{
    case TeamCoordinator = 'team_coordinator';
    case TeamLeader = 'team_leader';
    case PrimaryNegotiator = 'primary_negotiator';
    case SecondaryNegotiator = 'secondary_negotiator';
    case Recorder = 'recorder';
    case IntelligenceCoordinator = 'intelligence_coordinator';
    case MentalHealthCoordinator = 'mental_health_coordinator';
    case TacticalCommander = 'tactical_commander';
    case TacticalUser = 'tactical_user';
    case Administration = 'administration';

    public function label(): string
    {
        return match ($this) {
            self::TeamCoordinator => 'Team Coordinator',
            self::TeamLeader => 'Team Leader',
            self::PrimaryNegotiator => 'Primary Negotiator',
            self::SecondaryNegotiator => 'Secondary Negotiator',
            self::Recorder => 'Recorder',
            self::IntelligenceCoordinator => 'Intelligence Coordinator',
            self::MentalHealthCoordinator => 'Mental Health Coordinator',
            self::TacticalCommander => 'Tactical Commander',
            self::TacticalUser => 'Tactical User',
            self::Administration => 'Administration',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::TeamCoordinator => 'The team coordinator is responsible for the overall team\'s operations.',
            self::TeamLeader => 'The team leader is responsible for the overall team\'s operations.',
            self::PrimaryNegotiator => 'The primary negotiator is responsible for the overall team\'s operations.',
            self::SecondaryNegotiator => 'The secondary negotiator is responsible for the overall team\'s operations.',
            self::Recorder => 'The recorder is responsible for the overall team\'s operations.',
            self::IntelligenceCoordinator => 'The intelligence coordinator is responsible for the overall team\'s operations.',
            self::MentalHealthCoordinator => 'The mental health coordinator is responsible for the overall team\'s operations.',
            self::TacticalCommander => 'The tactical commander is responsible for the overall team\'s operations.',
            self::TacticalUser => 'The tactical user is any member of the team who is part of the tactical team.',
            self::Administration => 'The administration is responsible for the overall team\'s operations.',
        };
    }
}
