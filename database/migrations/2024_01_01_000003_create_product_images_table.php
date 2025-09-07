<?php

/*
  # Create product_images table

  1. New Tables
    - `product_images`
      - `id` (primary key)
      - `product_id` (foreign key to products)
      - `filename` (string)
      - `url` (string)
      - `order` (integer for ordering)
      - `created_at`, `updated_at` (timestamps)

  2. Security
    - Foreign key constraint to products table
    - Index on product_id and order for performance
*/

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('filename');
            $table->string('url');
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->index(['product_id', 'order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_images');
    }
};