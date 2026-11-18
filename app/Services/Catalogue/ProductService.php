<?php

namespace App\Services\Catalogue;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\User;
use App\Services\Access\AuditService;
use App\Support\Catalogue\SlugGenerator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductService
{
    public function __construct(private readonly AuditService $audit) {}

    public function create(User $actor, array $data, array $images = []): Product
    {
        return DB::transaction(function () use ($actor, $data, $images): Product {
            $product = Product::create($this->attributes($data));

            $this->syncCollections($product, $data['collections'] ?? []);
            $this->syncInventory($product, $data['inventory'] ?? []);
            $this->syncImages($product, $images, $data);

            $this->audit->record(
                'product.created',
                $actor,
                Product::class,
                $product->id,
                ['name' => $product->name, 'sku' => $product->sku],
            );

            return $product->load(['category', 'collections', 'images', 'inventory']);
        });
    }

    public function update(User $actor, Product $product, array $data, array $images = []): Product
    {
        return DB::transaction(function () use ($actor, $product, $data, $images): Product {
            $product->update($this->attributes($data, $product));

            $this->syncCollections($product, $data['collections'] ?? []);
            $this->syncInventory($product, $data['inventory'] ?? []);
            $this->syncImages($product, $images, $data);

            $this->audit->record(
                'product.updated',
                $actor,
                Product::class,
                $product->id,
                ['name' => $product->name, 'sku' => $product->sku],
            );

            return $product->load(['category', 'collections', 'images', 'inventory']);
        });
    }

    public function delete(User $actor, Product $product): void
    {
        DB::transaction(function () use ($actor, $product): void {
            $product->delete();

            $this->audit->record(
                'product.deleted',
                $actor,
                Product::class,
                $product->id,
                ['name' => $product->name, 'sku' => $product->sku],
            );
        });
    }

    private function attributes(array $data, ?Product $product = null): array
    {
        $attributes = Arr::only($data, [
            'category_id',
            'name',
            'slug',
            'sku',
            'short_description',
            'description',
            'price',
            'compare_at_price',
            'currency',
            'status',
            'is_featured',
            'metadata',
        ]);

        $attributes['slug'] = filled($attributes['slug'] ?? null)
            ? $attributes['slug']
            : SlugGenerator::generate($attributes['name'], Product::class, $product?->id);

        $attributes['category_id'] = (int) $attributes['category_id'];
        $attributes['currency'] = Str::upper($attributes['currency'] ?? 'NGN');
        $attributes['is_featured'] = (bool) ($attributes['is_featured'] ?? false);

        return $attributes;
    }

    private function syncCollections(Product $product, array $collectionIds): void
    {
        $payload = collect($collectionIds)
            ->filter()
            ->unique()
            ->values()
            ->mapWithKeys(fn (int|string $id, int $index): array => [(int) $id => ['sort_order' => $index]])
            ->all();

        $product->collections()->sync($payload);
    }

    private function syncInventory(Product $product, array $data): void
    {
        $inventory = [
            'quantity' => (int) ($data['quantity'] ?? 0),
            'reserved' => (int) ($data['reserved'] ?? 0),
            'low_stock_threshold' => (int) ($data['low_stock_threshold'] ?? 5),
            'track_inventory' => (bool) ($data['track_inventory'] ?? true),
        ];

        if ($inventory['reserved'] > $inventory['quantity']) {
            $inventory['reserved'] = $inventory['quantity'];
        }

        $product->inventory()->updateOrCreate(['product_id' => $product->id], $inventory);
    }

    private function syncImages(Product $product, array $images, array $data): void
    {
        $removeIds = collect($data['remove_images'] ?? [])->map(fn ($id): int => (int) $id)->all();

        if ($removeIds !== []) {
            $removable = ProductImage::where('product_id', $product->id)->whereIn('id', $removeIds)->get();

            foreach ($removable as $image) {
                if (Str::startsWith($image->path, 'products/')) {
                    Storage::disk('public')->delete($image->path);
                }

                $image->delete();
            }
        }

        $sortOrder = (int) ProductImage::where('product_id', $product->id)->max('sort_order') + 1;
        $primaryId = ! empty($data['primary_image_id']) ? (int) $data['primary_image_id'] : null;
        $hasPrimary = ProductImage::where('product_id', $product->id)->where('is_primary', true)->exists();

        foreach ($images as $image) {
            if (! $image instanceof UploadedFile) {
                continue;
            }

            $path = $image->store('products', 'public');

            ProductImage::create([
                'product_id' => $product->id,
                'path' => $path,
                'alt_text' => $product->name,
                'sort_order' => $sortOrder,
                'is_primary' => $primaryId === null && ! $hasPrimary,
            ]);

            if ($primaryId === null) {
                $hasPrimary = true;
            }

            $sortOrder++;
        }

        if ($primaryId !== null) {
            ProductImage::where('product_id', $product->id)->update(['is_primary' => false]);
            ProductImage::where('product_id', $product->id)->where('id', $primaryId)->update(['is_primary' => true]);
        }

        if (! ProductImage::where('product_id', $product->id)->where('is_primary', true)->exists()) {
            $first = ProductImage::where('product_id', $product->id)->orderBy('sort_order')->first();
            $first?->update(['is_primary' => true]);
        }
    }
}
