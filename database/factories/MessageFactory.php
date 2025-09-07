<?php

namespace Database\Factories;

use App\Models\Message;
use Illuminate\Database\Eloquent\Factories\Factory;

class MessageFactory extends Factory
{
    protected $model = Message::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'phone' => fake()->optional(0.8)->phoneNumber(),
            'message' => fake()->paragraph(3),
            'type' => fake()->randomElement([Message::TYPE_CONTACT, Message::TYPE_SERVICE_REQUEST]),
            'status' => fake()->randomElement([Message::STATUS_NEW, Message::STATUS_READ, Message::STATUS_CLOSED]),
        ];
    }

    public function contact(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => Message::TYPE_CONTACT,
        ]);
    }

    public function serviceRequest(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => Message::TYPE_SERVICE_REQUEST,
        ]);
    }

    public function unread(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Message::STATUS_NEW,
        ]);
    }
}