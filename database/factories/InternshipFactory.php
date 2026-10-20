<?php

namespace Database\Factories;

use App\Enums\InternshipStatus;
use App\Models\Internship;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Internship>
 */
class InternshipFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $start = fake()->dateTimeBetween('-6 months', '-1 month');

        return [
            'intern_id' => User::factory()->intern(),
            'start_date' => $start,
            'end_date' => (clone $start)->modify('+6 months'),
            'internship_period' => 6,
            'position' => fake()->jobTitle(),
            'status' => InternshipStatus::InPeriod->value,
        ];
    }

    /**
     * An internship whose end date has passed but whose status was not
     * updated yet.
     */
    public function overdue(): static
    {
        return $this->state(fn (array $attributes) => [
            'end_date' => now()->subDay(),
            'status' => InternshipStatus::InPeriod->value,
        ]);
    }

    /**
     * An internship that is already closed.
     */
    public function finished(): static
    {
        return $this->state(fn (array $attributes) => [
            'end_date' => now()->subDay(),
            'status' => InternshipStatus::Finished->value,
        ]);
    }
}
