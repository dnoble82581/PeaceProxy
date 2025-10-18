<?php

namespace App\Services\Team;

use App\Models\Team;

class TeamFetchingService
{
    public function __construct()
    {
    }

    public function fetchTeamOptions(): array
    {
        $teams = Team::all(['id', 'name']);

        return $teams->map(function ($team) {
            return [
                'label' => $team->name,
                'value' => $team->id,
            ];
        })->toArray();
    }
}
