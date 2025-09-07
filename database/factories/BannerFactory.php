<?php

namespace Database\Factories;

use App\Models\Banner;
use Illuminate\Database\Eloquent\Factories\Factory;

class BannerFactory extends Factory
{
    protected $model = Banner::class;

    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'tagline' => fake()->sentence(8),
            'image' => 'https://images.pexels.com/photos/1536619/pexels-photo-1536619.jpeg',
            'position' => fake()->numberBetween(1, 10),
            'is_active' => fake()->boolean(85),
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}