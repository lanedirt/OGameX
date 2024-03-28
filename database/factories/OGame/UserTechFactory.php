<?php

namespace Database\Factories\OGame;

use Illuminate\Database\Eloquent\Factories\Factory;
use OGame\UserTech;

/**
 * @extends Factory<UserTech>
 */
class UserTechFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UserTech::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'energy_technology' => 0,
        ];
    }
}
