<?php

namespace Database\Factories;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class CartItemFactory extends Factory
{
    protected $model = CartItem::class;

    public function definition(): array
    {
        $product = Product::inRandomOrder()->first();
        
        return [
            'cart_id' => Cart::factory(),
            'product_id' => $product->id,
            'quantity' => fake()->numberBetween(1, 5),
            'unit_price_cents' => $product->discounted_price_cents,
        ];
    }
}