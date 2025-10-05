<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(TenantTeamSeeder::class);
        //        // Create 10 tenants
        //        $tenants = Tenant::factory(10)->create();
        //
        //        foreach ($tenants as $tenant) {
        //            // Create 5 users for each tenant
        //            $users = User::factory(5)->create(['tenant_id' => $tenant->id]);
        //
        //            // Create 2 negotiations for each tenant
        //            $negotiations = Negotiation::factory(2)->create(['tenant_id' => $tenant->id]);
        //
        //            foreach ($negotiations as $negotiation) {
        //                // Create 1-3 subjects for each negotiation
        //                $subjects = Subject::factory(rand(1, 3))->create(['tenant_id' => $tenant->id]);
        //
        //                // Attach subjects to the negotiation with roles
        //                foreach ($subjects as $index => $subject) {
        //                    // First subject is primary, others are secondary or tertiary
        //                    $role = $index === 0 ? 'primary' : ($index === 1 ? 'secondary' : 'tertiary');
        //                    $negotiation->subjects()->attach($subject->id, ['role' => $role]);
        //                }
        //
        //                // Attach some users to the negotiation
        //                $negotiationUsers = $users->random(rand(1, 3));
        //                foreach ($negotiationUsers as $user) {
        //                    $negotiation->users()->attach($user->id, [
        //                        'role' => fake()->randomElement(UserNegotiationRole::cases())->value,
        //                        'status' => 'active',
        //                        'joined_at' => now(),
        //                    ]);
        //                }
        //            }
        //        }
    }

    /**
     * Get a random negotiation role for a user.
     */
    private function getRandomRole(): string
    {
        $roles = ['negotiator', 'team_lead', 'scribe', 'intelligence', 'tactical', 'observer'];
        return $roles[array_rand($roles)];
    }
}
