<?php

namespace Database\Factories;

use App\Enums\ReportState;
use App\Enums\ReportType;
use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Report>
 */
class ReportFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'content' => fake()->paragraph(),
            'type' => ReportType::Weekly->value,
            'intern_id' => User::factory()->intern(),
            'supervisor_id' => User::factory()->supervisor(),
            'state' => ReportState::Pending->value,
        ];
    }

    /**
     * A final report waiting for a decision.
     */
    public function final(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => ReportType::Final->value,
            'supervisor_id' => null,
        ]);
    }

    /**
     * An approved report.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'state' => ReportState::Approved->value,
        ]);
    }

    /**
     * A rejected report.
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'state' => ReportState::Rejected->value,
            'reject_reason' => fake()->sentence(),
        ]);
    }
}
