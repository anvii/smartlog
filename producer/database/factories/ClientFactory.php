<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

/**
 * @extends Factory<Client>
 */
class ClientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => static::fakePhone(),
        ];
    }

    private static function fakePhone()
    {
        $phone='+';
        for($i=0; $i<10; $i++) {
            $phone .= strval(random_int(0, 9));
        }
        return $phone;
    }
}
