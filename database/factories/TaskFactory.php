<?php

namespace Database\Factories;

use App\Enums\TaskState;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'intern_id' => User::factory()->intern(),
            'supervisor_id' => User::factory()->supervisor(),
            'state' => TaskState::Pending->value,
            'limit_date' => now()->addWeek(),
        ];
    }

    /**
     * A task whose deadline is in the past.
     */
    public function overdue(): static
    {
        return $this->state(fn (array $attributes) => [
            'limit_date' => now()->subDay(),
        ]);
    }

    /**
     * A task that the intern already completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'state' => TaskState::Completed->value,
        ]);
    }
}
