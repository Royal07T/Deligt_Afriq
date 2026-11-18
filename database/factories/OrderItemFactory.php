<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'product_id' => Product::factory(),
            'name' => fake()->words(3, true),
            'sku' => 'DA-'.fake()->unique()->bothify('????-####'),
            'quantity' => fake()->numberBetween(1, 3),
            'unit_price' => '25000.00',
            'line_total' => '50000.00',
        ];
    }
}
