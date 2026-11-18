<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Collection;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductImage;
use App\Support\Catalogue\ProductStatus;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categoryIds = Category::query()->pluck('id', 'slug');
        $collectionIds = Collection::query()->pluck('id', 'slug');

        $products = [
            [
                'slug' => 'custom-statement-gown',
                'name' => 'Custom Statement Gown',
                'sku' => 'DA-BSG-001',
                'category' => 'bespoke-fashion',
                'short_description' => 'A made-to-measure gown designed around your occasion and personal style.',
                'description' => 'A bespoke Delight Afriq gown created through consultation, fabric selection and tailored fittings. Each piece is made for the individual wearing it.',
                'price' => 185000,
                'compare_at_price' => null,
                'is_featured' => true,
                'image' => 'media/images/Custom Statement Gown.png',
                'collections' => ['occasion-edit', 'heritage-essentials'],
                'quantity' => 4,
                'low_stock_threshold' => 3,
            ],
            [
                'slug' => 'modern-heritage-set',
                'name' => 'Modern Heritage Set',
                'sku' => 'DA-BSG-002',
                'category' => 'bespoke-fashion',
                'short_description' => 'A coordinated set that balances African heritage with contemporary tailoring.',
                'description' => 'A refined two-piece set with clean lines and heritage-inspired detailing, suitable for elevated occasions and editorial moments.',
                'price' => 145000,
                'compare_at_price' => null,
                'is_featured' => true,
                'image' => 'media/images/Modern Heritage Set.png',
                'collections' => ['modern-heritage'],
                'quantity' => 6,
                'low_stock_threshold' => 4,
            ],
            [
                'slug' => 'family-ensemble',
                'name' => 'Family Ensemble',
                'sku' => 'DA-WRT-001',
                'category' => 'womens-ready-to-wear',
                'short_description' => 'A joyful coordinated look for family celebrations and portraits.',
                'description' => 'A ready-to-wear ensemble inspired by shared celebration, crafted in expressive African-print fabric.',
                'price' => 95000,
                'compare_at_price' => null,
                'is_featured' => true,
                'image' => 'media/images/Family.png',
                'collections' => ['heritage-essentials'],
                'quantity' => 12,
                'low_stock_threshold' => 5,
            ],
            [
                'slug' => 'silk-ankara-dress',
                'name' => 'Silk Ankara Dress',
                'sku' => 'DA-WRT-002',
                'category' => 'womens-ready-to-wear',
                'short_description' => 'An elegant dress with a fluid silhouette and vibrant Ankara print.',
                'description' => 'A versatile occasion dress that moves easily from daytime events to evening celebrations.',
                'price' => 78000,
                'compare_at_price' => 95000,
                'is_featured' => true,
                'image' => 'media/images/wmremove-transformed (5).jpeg',
                'collections' => ['modern-heritage'],
                'quantity' => 8,
                'low_stock_threshold' => 5,
            ],
            [
                'slug' => 'tailored-ankara-shirt',
                'name' => 'Tailored Ankara Shirt',
                'sku' => 'DA-MRT-001',
                'category' => 'mens-ready-to-wear',
                'short_description' => 'A sharp tailored shirt in statement African-print fabric.',
                'description' => 'A modern wardrobe staple cut for a clean fit and finished with thoughtful print placement.',
                'price' => 62000,
                'compare_at_price' => null,
                'is_featured' => false,
                'image' => 'media/images/men ankara.png',
                'collections' => ['heritage-essentials'],
                'quantity' => 15,
                'low_stock_threshold' => 5,
            ],
            [
                'slug' => 'heritage-two-piece',
                'name' => 'Heritage Two-Piece',
                'sku' => 'DA-MRT-002',
                'category' => 'mens-ready-to-wear',
                'short_description' => 'A coordinated two-piece look with a contemporary edge.',
                'description' => 'A statement set for men who value cultural detail, precise tailoring and effortless confidence.',
                'price' => 88000,
                'compare_at_price' => null,
                'is_featured' => false,
                'image' => 'media/images/wmremove-transformed (1).jpeg',
                'collections' => ['modern-heritage'],
                'quantity' => 9,
                'low_stock_threshold' => 5,
            ],
            [
                'slug' => 'ankara-statement-bag',
                'name' => 'Ankara Statement Bag',
                'sku' => 'DA-ACC-001',
                'category' => 'ankara-shoes-bags',
                'short_description' => 'A bold bag that adds heritage print to any outfit.',
                'description' => 'A structured accessory finished in expressive Ankara fabric for everyday or occasion styling.',
                'price' => 38000,
                'compare_at_price' => null,
                'is_featured' => false,
                'image' => 'media/images/Ankara bag.png',
                'collections' => ['statement-accessories'],
                'quantity' => 20,
                'low_stock_threshold' => 5,
            ],
            [
                'slug' => 'ankara-shoe-pair',
                'name' => 'Ankara Shoe Pair',
                'sku' => 'DA-ACC-002',
                'category' => 'ankara-shoes-bags',
                'short_description' => 'Statement footwear finished in vibrant Ankara fabric.',
                'description' => 'A distinctive shoe pair designed to complete African-inspired looks with colour and craft.',
                'price' => 45000,
                'compare_at_price' => null,
                'is_featured' => false,
                'image' => 'media/images/Ankara shoe pair.png',
                'collections' => ['statement-accessories'],
                'quantity' => 3,
                'low_stock_threshold' => 5,
            ],
        ];

        foreach ($products as $item) {
            $product = Product::updateOrCreate(
                ['slug' => $item['slug']],
                [
                    'category_id' => $categoryIds[$item['category']],
                    'name' => $item['name'],
                    'sku' => $item['sku'],
                    'short_description' => $item['short_description'],
                    'description' => $item['description'],
                    'price' => $item['price'],
                    'compare_at_price' => $item['compare_at_price'],
                    'currency' => 'NGN',
                    'status' => ProductStatus::ACTIVE,
                    'is_featured' => $item['is_featured'],
                ],
            );

            $collectionPayload = collect($item['collections'])
                ->mapWithKeys(fn (string $slug, int $index): array => [$collectionIds[$slug] => ['sort_order' => $index]])
                ->all();

            $product->collections()->sync($collectionPayload);

            Inventory::updateOrCreate(
                ['product_id' => $product->id],
                [
                    'quantity' => $item['quantity'],
                    'reserved' => 0,
                    'low_stock_threshold' => $item['low_stock_threshold'],
                    'track_inventory' => true,
                ],
            );

            ProductImage::updateOrCreate(
                ['product_id' => $product->id, 'path' => $item['image']],
                [
                    'alt_text' => $item['name'],
                    'sort_order' => 0,
                    'is_primary' => true,
                ],
            );
        }
    }
}
