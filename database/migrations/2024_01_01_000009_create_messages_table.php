<?php

/*
  # Create messages table

  1. New Tables
    - `messages`
      - `id` (primary key)
      - `name` (string, customer name)
      - `email` (string, customer email)
      - `phone` (string, nullable)
      - `message` (text, message content)
      - `type` (enum: contact, service_request)
      - `status` (enum: new, read, closed)
      - `created_at`, `updated_at` (timestamps)

  2. Security
    - Index on status and type for admin filtering
    - Index on created_at for chronological ordering
*/

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->text('message');
            $table->enum('type', ['contact', 'service_request'])->default('contact');
            $table->enum('status', ['new', 'read', 'closed'])->default('new');
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};