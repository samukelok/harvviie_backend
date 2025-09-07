<?php

/*
  # Create banners table

  1. New Tables
    - `banners`
      - `id` (primary key)
      - `title` (string, required)
      - `tagline` (string, nullable)
      - `image` (string, required)
      - `position` (integer for ordering)
      - `is_active` (boolean, default true)
      - `created_at`, `updated_at` (timestamps)

  2. Security
    - Index on position and is_active for frontend queries
*/

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('tagline')->nullable();
            $table->string('image');
            $table->integer('position')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['position', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('banners');
    }
};