<?php

namespace Database\Seeders;

use App\Models\Cotisation;
use App\Models\Group;
use App\Models\GroupParticipant;
use App\Models\Invitation;
use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create a pool of users
        $users = User::factory(8)->create();

        // Predictable test account
        $testUser = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        /** @var \Illuminate\Support\Collection<int, \App\Models\User> $userPool */
        $userPool = $users->push($testUser);

        Group::factory(6)->make()->each(function (Group $group) use ($userPool) {
            $creator = $userPool->random();

            $group->created_by = $creator->id;
            $group->balance = 0;
            $group->save();

            $participantIds = $userPool->pluck('id')->shuffle()->take(rand(3, 7));
            if (! $participantIds->contains($creator->id)) {
                $participantIds->push($creator->id);
            }

            $participantIds->each(function ($userId) use ($group, $creator) {
                GroupParticipant::create([
                    'group_id' => $group->id,
                    'user_id' => $userId,
                    'montant_par_defaut' => fake()->randomFloat(2, 5, 50),
                    'date_ajout' => Carbon::now()->subDays(rand(10, 120)),
                    'statut' => 'actif',
                    'is_admin' => $userId === $group->created_by,
                ]);
            });

            $participants = GroupParticipant::where('group_id', $group->id)->get();

            $balance = 0;

            $cotisationCount = rand(3, 8);
            for ($i = 0; $i < $cotisationCount; $i++) {
                /** @var GroupParticipant $participant */
                $participant = $participants->random();
                $amount = fake()->randomFloat(2, 10, 150);

                Cotisation::create([
                    'group_id' => $group->id,
                    'user_id' => $participant->user_id,
                    'montant' => $amount,
                    'preuve_path' => null,
                    'date_versement' => Carbon::now()->subDays(rand(0, 45)),
                    'created_by' => $group->created_by,
                ]);

                $balance += $amount;
            }

            $paymentIterations = rand(1, 3);
            for ($j = 0; $j < $paymentIterations && $balance > 0; $j++) {
                /** @var GroupParticipant $recipient */
                $recipient = $participants->where('user_id', '!=', $group->created_by)->random();
                $amount = min($balance, fake()->randomFloat(2, 5, 120));

                Payment::create([
                    'group_id' => $group->id,
                    'user_id' => $recipient->user_id,
                    'group_participant_id' => $recipient->id,
                    'montant' => $amount,
                    'date_paiement' => Carbon::now()->subDays(rand(0, 20)),
                    'motif' => fake()->optional()->sentence(4),
                    'created_by' => $group->created_by,
                ]);

                $balance -= $amount;
            }

            $group->update(['balance' => $balance]);

            $this->createInvitations($group);
        });
    }

    protected function createInvitations(Group $group): void
    {
        $invitationCount = rand(0, 3);

        for ($i = 0; $i < $invitationCount; $i++) {
            Invitation::create([
                'group_id' => $group->id,
                'email' => 'invite+'.Str::random(5).'@example.com',
                'token' => Str::uuid(),
                'status' => 'pending',
            ]);
        }
    }
}
