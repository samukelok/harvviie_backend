<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = fake()->words(3, true);
        $price = fake()->numberBetween(1000, 50000); // Price in cents

        return [
            'sku' => 'HV-' . fake()->unique()->numerify('######'),
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => fake()->paragraph(3),
            'price_cents' => $price,
            'discount_percent' => fake()->optional(0.3)->numberBetween(5, 30),
            'stock' => fake()->numberBetween(0, 100),
            'is_active' => fake()->boolean(85),
            'metadata' => [
                'material' => fake()->randomElement(['Cotton', 'Polyester', 'Wool', 'Silk', 'Linen']),
                'care_instructions' => fake()->randomElement([
                    'Machine wash cold',
                    'Hand wash only',
                    'Dry clean only',
                    'Machine wash warm'
                ]),
                'origin' => 'South Africa',
            ],
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function outOfStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock' => 0,
        ]);
    }

    public function onSale(): static
    {
        return $this->state(fn (array $attributes) => [
            'discount_percent' => fake()->numberBetween(10, 50),
        ]);
    }
}