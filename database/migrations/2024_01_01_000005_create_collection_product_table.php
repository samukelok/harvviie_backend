<?php

/*
  # Create collection_product pivot table

  1. New Tables
    - `collection_product` (pivot table)
      - `collection_id` (foreign key to collections)
      - `product_id` (foreign key to products)
      - `position` (integer for ordering within collection)

  2. Security
    - Foreign key constraints to both parent tables
    - Unique constraint on collection_id + product_id
    - Index on position for sorting
*/

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('collection_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('collection_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->integer('position')->default(0);
            $table->timestamps();

            $table->unique(['collection_id', 'product_id']);
            $table->index('position');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('collection_product');
    }
};