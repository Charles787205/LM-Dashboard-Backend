<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SuccessfulDeliveries>
 */
class SuccessfulDeliveriesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'two_w' => fake()->numberBetween(20, 100),
            'three_w' => fake()->numberBetween(10, 50),
            'four_w' => fake()->numberBetween(5, 30),
        ];
    }
}
