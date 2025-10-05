<?php

namespace Database\Seeders;

use App\Enums\Team\TeamDiscipline;
use App\Models\Team;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class TenantTeamSeeder extends Seeder
{
    public function run(): void
    {
        foreach (Tenant::all() as $tenant) {
            foreach (TeamDiscipline::cases() as $d) {
                Team::firstOrCreate(
                    ['tenant_id' => $tenant->id, 'slug' => $d->value],
                    ['name' => ucfirst($d->value)]
                );
            }
        }

    }
}
