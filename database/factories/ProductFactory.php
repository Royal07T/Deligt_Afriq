<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Inventory;
use App\Models\Product;
use App\Support\Catalogue\ProductStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'name' => ucfirst(fake()->unique()->words(3, true)),
            'slug' => Str::slug(fake()->unique()->words(4, true)),
            'sku' => 'DA-'.strtoupper(fake()->unique()->bothify('????-####')),
            'short_description' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'price' => fake()->randomFloat(2, 1000, 50000),
            'compare_at_price' => null,
            'currency' => 'NGN',
            'status' => ProductStatus::ACTIVE,
            'is_featured' => false,
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Product $product): void {
            Inventory::create([
                'product_id' => $product->id,
                'quantity' => 10,
                'reserved' => 0,
                'low_stock_threshold' => 5,
                'track_inventory' => true,
            ]);
        });
    }
}
