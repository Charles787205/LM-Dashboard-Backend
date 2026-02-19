<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FailedDeliveries>
 */
class FailedDeliveriesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'two_w' => fake()->numberBetween(0, 15),
            'three_w' => fake()->numberBetween(0, 10),
            'four_w' => fake()->numberBetween(0, 5),
        ];
    }
}
