<?php

namespace Database\Factories;

use App\Models\Order;
use App\Support\Payments\PaymentTransactionStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PaymentTransactionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'reference' => 'PAY-'.strtoupper(Str::random(16)),
            'provider' => 'paystack',
            'status' => PaymentTransactionStatus::PENDING,
            'amount_minor' => 5250000,
            'currency' => 'NGN',
            'external_id' => null,
            'payload' => null,
            'paid_at' => null,
        ];
    }
}
