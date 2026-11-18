<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductImageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'path' => 'products/'.fake()->uuid().'.jpg',
            'alt_text' => fake()->sentence(3),
            'sort_order' => fake()->numberBetween(0, 5),
            'is_primary' => false,
        ];
    }
}
