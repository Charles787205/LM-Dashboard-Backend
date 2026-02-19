<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Client;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Hub>
 */
class HubFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->city() . ' Hub',
            'hub_lead_id' => User::inRandomOrder()->first()?->id ?? User::factory()->create()->id,
            'client_id' => Client::inRandomOrder()->first()?->id ?? Client::factory()->create()->id,
        ];
    }
}
