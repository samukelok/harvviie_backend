<?php

namespace Database\Factories;

use App\Models\Cart;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CartFactory extends Factory
{
    protected $model = Cart::class;

    public function definition(): array
    {
        return [
            'user_id' => fake()->optional(0.7)->randomElement(User::pluck('id')),
            'session_id' => fake()->optional(0.3)->uuid(),
            'status' => fake()->randomElement([Cart::STATUS_ACTIVE, Cart::STATUS_ABANDONED]),
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Cart::STATUS_ACTIVE,
        ]);
    }

    public function abandoned(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Cart::STATUS_ABANDONED,
        ]);
    }

    public function converted(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Cart::STATUS_CONVERTED,
        ]);
    }
}