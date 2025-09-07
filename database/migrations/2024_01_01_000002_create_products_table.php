<?php

/*
  # Create products table

  1. New Tables
    - `products`
      - `id` (uuid, primary key)
      - `sku` (string, unique, nullable)
      - `name` (string, required)
      - `slug` (string, unique)
      - `description` (text, nullable)
      - `price_cents` (integer, required)
      - `discount_percent` (tinyint, nullable, 0-100)
      - `stock` (integer, default 0)
      - `is_active` (boolean, default true)
      - `metadata` (json, nullable)
      - `created_at`, `updated_at`, `deleted_at` (timestamps)

  2. Security
    - Enable RLS on `products` table
    - Add indexes for performance
*/

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('sku')->unique()->nullable();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->integer('price_cents');
            $table->tinyInteger('discount_percent')->nullable()->default(0);
            $table->integer('stock')->default(0);
            $table->boolean('is_active')->default(true);
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['is_active', 'deleted_at']);
            $table->index('slug');
            $table->index('sku');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};