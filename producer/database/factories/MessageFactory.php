<?php

namespace Database\Factories;

use App\Models\Message;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

/**
 * @extends Factory<Message>
 */
class MessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'client_id' => random_int(1, 20),
            'channel' => Arr::random(['email', 'sms']),
            'message' => fake()->text(200),
            'priority' => Arr::random(['normal', 'high']),
            'status' => 'created',
        ];
    }
}
