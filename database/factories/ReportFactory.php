<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Hub;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Report>
 */
class ReportFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $inbound = fake()->numberBetween(50, 500);
        $outbound = fake()->numberBetween(40, $inbound);
        $delivered = fake()->numberBetween(30, $outbound);
        $failed = fake()->numberBetween(0, $outbound - $delivered);
        $backlogs = $inbound - $outbound;
        $misroutes = fake()->numberBetween(0, 20);
        
        $successRate = $outbound > 0 ? round(($delivered / $outbound) * 100, 2) : 0;
        $failedRate = $outbound > 0 ? round(($failed / $outbound) * 100, 2) : 0;

        return [
            'hub_id' => Hub::inRandomOrder()->first()?->id ?? Hub::factory()->create()->id,
            'user_id' => User::inRandomOrder()->first()?->id ?? User::factory()->create()->id,
            'inbound' => $inbound,
            'outbound' => $outbound,
            'delivered' => $delivered,
            'backlogs' => $backlogs,
            'failed' => $failed,
            'misroutes' => $misroutes,
            'date' => fake()->dateTimeBetween('-6 months', 'now')->format('Y-m-d'),
            'sdod' => fake()->optional(0.3)->word(),
            'failed_rate' => $failedRate,
            'success_rate' => $successRate,
        ];
    }

    /**
     * Configure the factory to create related records.
     */
    public function configure()
    {
        return $this->afterCreating(function ($report) {
            // Create related records
            \App\Models\Trip::factory()->create(['report_id' => $report->id]);
            \App\Models\SuccessfulDeliveries::factory()->create(['report_id' => $report->id]);
            \App\Models\FailedDeliveries::factory()->create(['report_id' => $report->id]);
            \App\Models\Attendance::factory()->count(2)->create(['report_id' => $report->id]);
        });
    }
}
