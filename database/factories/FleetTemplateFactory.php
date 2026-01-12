<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use OGame\Models\FleetTemplate;

/**
 * @extends Factory<FleetTemplate>
 */
class FleetTemplateFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = FleetTemplate::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => 1,
            'name' => 'Test Fleet Template ' . $this->faker->word(),
            'ships' => [
                '204' => $this->faker->numberBetween(1, 200),
                '205' => $this->faker->numberBetween(1, 100),
                '206' => $this->faker->numberBetween(1, 50),
            ],
        ];
    }
}
