<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\GroupParticipant;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\GroupParticipant>
 */
class GroupParticipantFactory extends Factory
{
    protected $model = GroupParticipant::class;

    public function definition(): array
    {
        return [
            'group_id' => null,
            'user_id' => null,
            'montant_par_defaut' => $this->faker->randomFloat(2, 0, 100),
            'date_ajout' => now(),
            'statut' => 'actif',
            'is_admin' => false,
        ];
    }
}
