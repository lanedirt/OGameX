<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use OGame\Models\Planet;

/**
 * @extends Factory<Planet>
 */
class PlanetFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
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
            'deuterium_synthesizer' => 0,
            'deuterium_synthesizer_percent' => 10,
            'crystal_mine' => 0,
            'crystal_mine_percent' => 10,
            'planet_type' => 1,
            'diameter' => 300,
            'field_max' => rand(140, 250),
            'temp_min' => rand(0, 100),
            'temp_max' => rand(0, 100) + 40,
            'metal' => 0,
            'crystal' => 0,
            'deuterium' => 0,
            'fusion_plant' => 0,
        ];
    }
}
