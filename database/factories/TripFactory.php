<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Trip>
 */
class TripFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'two_w' => fake()->numberBetween(5, 50),
            'three_w' => fake()->numberBetween(5, 30),
            'four_w' => fake()->numberBetween(2, 20),
        ];
    }
}
