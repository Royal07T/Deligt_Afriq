<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Bespoke Fashion',
                'slug' => 'bespoke-fashion',
                'description' => 'Custom-made pieces designed around your style, event and vision.',
                'image_path' => 'media/images/Custom Statement Gown.png',
                'sort_order' => 1,
            ],
            [
                'name' => "Women's Ready-to-Wear",
                'slug' => 'womens-ready-to-wear',
                'description' => 'Elegant pieces crafted for confident everyday and occasion styling.',
                'image_path' => 'media/images/wmremove-transformed (5).jpeg',
                'sort_order' => 2,
            ],
            [
                'name' => "Men's Ready-to-Wear",
                'slug' => 'mens-ready-to-wear',
                'description' => 'Sophisticated styles for the modern gentleman who values detail.',
                'image_path' => 'media/images/men ankara.png',
                'sort_order' => 3,
            ],
            [
                'name' => 'Ankara Shoes & Bags',
                'slug' => 'ankara-shoes-bags',
                'description' => 'Statement accessories that complete your African-inspired look.',
                'image_path' => 'media/images/Ankara bag.png',
                'sort_order' => 4,
            ],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['slug' => $category['slug']],
                $category + ['is_active' => true],
            );
        }
    }
}
