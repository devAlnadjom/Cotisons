<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Group;
use App\Models\GroupParticipant;
use App\Models\Invitation;
use App\Models\Cotisation;
use Illuminate\Support\Str;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create users
        $users = User::factory(8)->create();

        // Ensure a predictable test user
        $testUser = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Create groups and attach participants
        $groups = Group::factory(4)->make()->each(function($g) use ($users, $testUser) {
            $g->created_by = $users->random()->id;
            $g->save();

            // Add between 2 and 6 participants including creator
            $participants = $users->random(rand(2,6));
            foreach ($participants as $p) {
                GroupParticipant::create([
                    'group_id' => $g->id,
                    'user_id' => $p->id,
                    'montant_par_defaut' => rand(5,50),
                    'date_ajout' => now(),
                    'statut' => 'actif',
                    'is_admin' => ($p->id === $g->created_by),
                ]);
            }

            // Add a couple of cotisations
            for ($i = 0; $i < rand(1,4); $i++) {
                Cotisation::create([
                    'group_id' => $g->id,
                    'user_id' => $participants->random()->id,
                    'montant' => rand(5,100),
                    'preuve_path' => null,
                    'date_versement' => now()->subDays(rand(0,60)),
                    'created_by' => $users->random()->id,
                ]);
            }

            // Add invitations
            for ($j = 0; $j < rand(0,3); $j++) {
                Invitation::create([
                    'group_id' => $g->id,
                    'email' => 'invite+' . Str::random(5) . '@example.com',
                    'token' => Str::random(40),
                    'status' => 'pending',
                ]);
            }
        });
    }
}
