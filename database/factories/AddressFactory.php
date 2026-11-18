<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AddressFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'label' => fake()->randomElement(['Home', 'Work', 'Studio']),
            'recipient_name' => fake()->name(),
            'phone' => '+234'.fake()->numerify('##########'),
            'line_1' => fake()->streetAddress(),
            'line_2' => null,
            'city' => 'Abuja',
            'state' => 'FCT',
            'postal_code' => fake()->numerify('######'),
            'country' => 'NG',
            'is_default' => false,
        ];
    }
}
