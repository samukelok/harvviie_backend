<?php

/*
  # Create orders table

  1. New Tables
    - `orders`
      - `id` (primary key)
      - `order_number` (string, unique)
      - `user_id` (foreign key to users, nullable)
      - `customer_name` (string)
      - `customer_email` (string)
      - `items` (json array of order items)
      - `amount_cents` (integer)
      - `status` (enum: pending, processing, shipped, delivered, cancelled)
      - `shipping_address` (json)
      - `placed_at` (timestamp)
      - `created_at`, `updated_at` (timestamps)

  2. Security
    - Index on order_number, status, and placed_at for queries
    - Foreign key to users (nullable for guest orders)
*/

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('customer_name');
            $table->string('customer_email');
            $table->json('items');
            $table->integer('amount_cents');
            $table->enum('status', ['pending', 'processing', 'shipped', 'delivered', 'cancelled'])
                  ->default('pending');
            $table->json('shipping_address')->nullable();
            $table->timestamp('placed_at');
            $table->timestamps();

            $table->index('order_number');
            $table->index(['status', 'placed_at']);
            $table->index('customer_email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};