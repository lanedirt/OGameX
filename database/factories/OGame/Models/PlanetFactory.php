<?php

namespace Database\Factories\OGame\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use OGame\Models\Planet;

/**
 * @extends Factory<Planet>
 */
class PlanetFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Planet::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'FakePlanetName',
            'metal_mine' => 0,
            'metal_mine_percent' => 10,
            'solar_plant' => 0,
            'solar_plant_percent' => 10,
        ];
    }
}
