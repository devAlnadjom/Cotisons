<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Invitation;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Invitation>
 */
class InvitationFactory extends Factory
{
    protected $model = Invitation::class;

    public function definition(): array
    {
        return [
            'group_id' => null,
            'email' => $this->faker->safeEmail(),
            'token' => Str::random(40),
            'status' => $this->faker->randomElement(['pending','accepted']),
        ];
    }
}
