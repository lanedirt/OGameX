<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use OGame\Models\UserTech;

/**
 * @extends Factory<UserTech>
 */
class UserTechFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
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
