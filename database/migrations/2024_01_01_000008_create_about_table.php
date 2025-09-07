<?php

/*
  # Create about table (singleton)

  1. New Tables
    - `about`
      - `id` (primary key, should only have one record)
      - `content` (longtext, main about content)
      - `milestones` (json array of milestone objects)
      - `updated_by_user_id` (foreign key to users)
      - `created_at`, `updated_at` (timestamps)

  2. Security
    - Foreign key to users for audit trail
    - This is a singleton table (should only contain one record)
*/

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('about', function (Blueprint $table) {
            $table->id();
            $table->longText('content');
            $table->json('milestones')->nullable();
            $table->foreignId('updated_by_user_id')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('about');
    }
};