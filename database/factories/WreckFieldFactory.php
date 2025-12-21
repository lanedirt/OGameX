<?php

namespace Database\Factories;

use OGame\Models\WreckField;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\OGame\Models\WreckField>
 */
class WreckFieldFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\OGame\Models\WreckField>
     */
    protected $model = WreckField::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'galaxy' => $this->faker->numberBetween(1, 9),
            'system' => $this->faker->numberBetween(1, 499),
            'planet' => $this->faker->numberBetween(1, 15),
            'owner_player_id' => 1,
            'created_at' => now(),
            'expires_at' => now()->addHours(72),
            'repair_started_at' => null,
            'repair_completed_at' => null,
            'space_dock_level' => null,
            'status' => 'active',
            'ship_data' => [
                [
                    'machine_name' => 'light_fighter',
                    'quantity' => $this->faker->numberBetween(1, 100),
                    'repair_progress' => 0,
                ]
            ],
        ];
    }

    /**
     * Indicate that the wreck field is currently being repaired.
     */
    public function repairing(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'repairing',
            'repair_started_at' => now()->subHours(1),
            'repair_completed_at' => now()->addHours(1),
            'space_dock_level' => $this->faker->numberBetween(1, 10),
            'ship_data' => [
                [
                    'machine_name' => 'light_fighter',
                    'quantity' => $this->faker->numberBetween(1, 100),
                    'repair_progress' => $this->faker->numberBetween(0, 100),
                ]
            ],
        ]);
    }

    /**
     * Indicate that the wreck field repairs are completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'repair_started_at' => now()->subHours(2),
            'repair_completed_at' => now()->subHours(1),
            'space_dock_level' => $this->faker->numberBetween(1, 10),
            'ship_data' => [
                [
                    'machine_name' => 'light_fighter',
                    'quantity' => $this->faker->numberBetween(1, 100),
                    'repair_progress' => 100,
                ]
            ],
        ]);
    }

    /**
     * Indicate that the wreck field has been burned.
     */
    public function burned(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'burned',
        ]);
    }

    /**
     * Indicate that the wreck field is expired.
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'expires_at' => now()->subHours(1),
        ]);
    }

    /**
     * Create a wreck field with multiple ship types.
     */
    public function withMultipleShips(): static
    {
        return $this->state(fn (array $attributes) => [
            'ship_data' => [
                [
                    'machine_name' => 'light_fighter',
                    'quantity' => $this->faker->numberBetween(1, 50),
                    'repair_progress' => 0,
                ],
                [
                    'machine_name' => 'heavy_fighter',
                    'quantity' => $this->faker->numberBetween(1, 30),
                    'repair_progress' => 0,
                ],
                [
                    'machine_name' => 'cruiser',
                    'quantity' => $this->faker->numberBetween(1, 20),
                    'repair_progress' => 0,
                ],
            ],
        ]);
    }
}
