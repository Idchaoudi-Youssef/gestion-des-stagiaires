<?php

namespace Database\Factories;

use App\Enums\UserType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'usertype' => UserType::Pending->value,
            'phone' => fake()->phoneNumber(),
            'address' => fake()->address(),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * An administrator.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'usertype' => UserType::Admin->value,
        ]);
    }

    /**
     * A supervisor.
     */
    public function supervisor(): static
    {
        return $this->state(fn (array $attributes) => [
            'usertype' => UserType::Supervisor->value,
        ]);
    }

    /**
     * An intern.
     */
    public function intern(): static
    {
        return $this->state(fn (array $attributes) => [
            'usertype' => UserType::Intern->value,
        ]);
    }

    /**
     * An account waiting for an administrator.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'usertype' => UserType::Pending->value,
        ]);
    }
}
