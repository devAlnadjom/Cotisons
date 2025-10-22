<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Cotisation;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cotisation>
 */
class CotisationFactory extends Factory
{
    protected $model = Cotisation::class;

    public function definition(): array
    {
        return [
            'group_id' => null,
            'user_id' => null,
            'montant' => $this->faker->randomFloat(2, 1, 200),
            'preuve_path' => null,
            'date_versement' => $this->faker->optional()->dateTimeBetween('-60 days', 'now'),
            'created_by' => null,
        ];
    }
}
