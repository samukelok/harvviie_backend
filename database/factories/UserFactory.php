<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
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
            'role' => fake()->randomElement([User::ROLE_ADMIN, User::ROLE_EDITOR, User::ROLE_CUSTOMER]),
            'phone' => fake()->optional(0.7)->phoneNumber(),
            'address' => fake()->optional(0.8)->randomElement([
                [
                    'street' => fake()->streetAddress(),
                    'city' => fake()->city(),
                    'postal_code' => fake()->postcode(),
                    'country' => 'South Africa',
                ],
                null
            ]),
            'remember_token' => Str::random(10),
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

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => User::ROLE_ADMIN,
        ]);
    }

    public function editor(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => User::ROLE_EDITOR,
        ]);
    }

    public function customer(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => User::ROLE_CUSTOMER,
        ]);
    }
}