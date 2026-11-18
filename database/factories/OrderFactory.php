<?php

namespace Database\Factories;

use App\Models\User;
use App\Support\Orders\OrderStatus;
use App\Support\Orders\PaymentMethod;
use App\Support\Orders\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class OrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'order_number' => 'DA-'.now()->format('Ymd').'-'.strtoupper(Str::random(6)),
            'user_id' => User::factory(),
            'status' => OrderStatus::PENDING,
            'payment_method' => PaymentMethod::WHATSAPP,
            'payment_status' => PaymentStatus::UNPAID,
            'payment_reference' => 'WHATSAPP-'.strtoupper(Str::random(10)),
            'currency' => 'NGN',
            'subtotal' => '50000.00',
            'shipping' => '2500.00',
            'discount' => '0.00',
            'total' => '52500.00',
            'shipping_name' => fake()->name(),
            'shipping_phone' => '+234'.fake()->numerify('##########'),
            'shipping_email' => fake()->safeEmail(),
            'shipping_line_1' => fake()->streetAddress(),
            'shipping_line_2' => null,
            'shipping_city' => 'Abuja',
            'shipping_state' => 'FCT',
            'shipping_postal_code' => fake()->numerify('######'),
            'shipping_country' => 'NG',
            'notes' => null,
            'placed_at' => now(),
        ];
    }
}
