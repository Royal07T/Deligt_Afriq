<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CategoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => ucfirst(fake()->unique()->words(2, true)),
            'slug' => Str::slug(fake()->unique()->words(3, true)),
            'description' => fake()->sentence(),
            'image_path' => 'media/images/Allin1.jpeg',
            'is_active' => true,
            'sort_order' => fake()->numberBetween(0, 20),
        ];
    }
}
