<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        $items = $this->generateOrderItems();
        $totalAmount = collect($items)->sum(fn($item) => $item['quantity'] * $item['unit_price_cents']);

        return [
            // Use Faker's unique() helper to avoid duplicates
            'order_number'   => fake()->unique()->regexify('HV-' . now()->format('Ymd') . '-[0-9]{6}'),
            'user_id'        => fake()->optional(0.7)->randomElement(User::pluck('id')),
            'customer_name'  => fake()->name(),
            'customer_email' => fake()->safeEmail(),
            'items'          => $items,
            'amount_cents'   => $totalAmount,
            'status'         => fake()->randomElement([
                Order::STATUS_PENDING,
                Order::STATUS_PROCESSING,
                Order::STATUS_SHIPPED,
                Order::STATUS_DELIVERED,
                Order::STATUS_CANCELLED,
            ]),
            'shipping_address' => [
                'name'           => fake()->name(),
                'address_line_1' => fake()->streetAddress(),
                'address_line_2' => fake()->optional()->secondaryAddress(),
                'city'           => fake()->city(),
                'postal_code'    => fake()->postcode(),
                'country'        => 'South Africa',
            ],
            'placed_at' => fake()->dateTimeBetween('-30 days', 'now'),
        ];
    }

    private function generateOrderItems(): array
    {
        $products = Product::inRandomOrder()->limit(fake()->numberBetween(1, 4))->get();
        $items = [];

        foreach ($products as $product) {
            $items[] = [
                'product_id'      => $product->id,
                'product_name'    => $product->name,
                'quantity'        => fake()->numberBetween(1, 3),
                'unit_price_cents'=> $product->price_cents,
            ];
        }

        return $items;
    }

    public function pending(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => Order::STATUS_PENDING,
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => Order::STATUS_DELIVERED,
        ]);
    }
}
