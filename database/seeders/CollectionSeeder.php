<?php

namespace Database\Seeders;

use App\Models\Collection;
use Illuminate\Database\Seeder;

class CollectionSeeder extends Seeder
{
    public function run(): void
    {
        $collections = [
            [
                'name' => 'Heritage Essentials',
                'slug' => 'heritage-essentials',
                'description' => 'Everyday pieces rooted in African print and modern tailoring.',
                'image_path' => 'media/images/Allin1.jpeg',
                'sort_order' => 1,
            ],
            [
                'name' => 'Occasion Edit',
                'slug' => 'occasion-edit',
                'description' => 'Statement looks for weddings, celebrations and milestone moments.',
                'image_path' => 'media/images/Custom Statement Gown1.png',
                'sort_order' => 2,
            ],
            [
                'name' => 'Modern Heritage',
                'slug' => 'modern-heritage',
                'description' => 'Contemporary silhouettes with a cultural point of view.',
                'image_path' => 'media/images/Modern Heritage Set.png',
                'sort_order' => 3,
            ],
            [
                'name' => 'Statement Accessories',
                'slug' => 'statement-accessories',
                'description' => 'Ankara shoes, bags and finishing details.',
                'image_path' => 'media/images/Ankara shoe pair.png',
                'sort_order' => 4,
            ],
        ];

        foreach ($collections as $collection) {
            Collection::updateOrCreate(
                ['slug' => $collection['slug']],
                $collection + ['is_active' => true],
            );
        }
    }
}
