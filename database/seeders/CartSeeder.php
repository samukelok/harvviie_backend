<?php

namespace Database\Seeders;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class CartSeeder extends Seeder
{
    public function run(): void
    {
        // Create some active carts for customers
        $customers = User::where('role', User::ROLE_CUSTOMER)->get();
        
        foreach ($customers->take(5) as $customer) {
            $cart = Cart::create([
                'user_id' => $customer->id,
                'status' => Cart::STATUS_ACTIVE,
            ]);

            // Add 2-4 random products to each cart
            $products = Product::inRandomOrder()->limit(fake()->numberBetween(2, 4))->get();
            
            foreach ($products as $product) {
                CartItem::create([
                    'cart_id' => $cart->id,
                    'product_id' => $product->id,
                    'quantity' => fake()->numberBetween(1, 3),
                    'unit_price_cents' => $product->discounted_price_cents,
                ]);
            }
        }

        // Create some abandoned carts
        Cart::factory(3)->abandoned()->create()->each(function ($cart) {
            $products = Product::inRandomOrder()->limit(fake()->numberBetween(1, 3))->get();
            
            foreach ($products as $product) {
                CartItem::create([
                    'cart_id' => $cart->id,
                    'product_id' => $product->id,
                    'quantity' => fake()->numberBetween(1, 2),
                    'unit_price_cents' => $product->discounted_price_cents,
                ]);
            }
        });
    }
}