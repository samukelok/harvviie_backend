<?php

/*
  # Create collections table

  1. New Tables
    - `collections`
      - `id` (primary key)
      - `name` (string, required)
      - `slug` (string, unique)
      - `description` (text, nullable)
      - `cover_image` (string, nullable)
      - `is_active` (boolean, default true)
      - `created_at`, `updated_at`, `deleted_at` (timestamps)

  2. Security
    - Index on slug and is_active for performance
    - Soft deletes enabled
*/

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('collections', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('cover_image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['is_active', 'deleted_at']);
            $table->index('slug');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('collections');
    }
};