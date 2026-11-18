<?php

namespace App\Services\Catalogue;

use App\Models\Category;
use App\Support\Catalogue\SlugGenerator;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;

class CategoryService
{
    public function create(array $data): Category
    {
        return Category::create($this->attributes($data));
    }

    public function update(Category $category, array $data): Category
    {
        $category->update($this->attributes($data, $category));

        return $category->fresh();
    }

    public function delete(Category $category): void
    {
        if ($category->products()->exists()) {
            throw ValidationException::withMessages([
                'category' => 'Move or delete the products in this category first.',
            ]);
        }

        $category->delete();
    }

    private function attributes(array $data, ?Category $category = null): array
    {
        $attributes = Arr::only($data, [
            'name',
            'slug',
            'description',
            'image_path',
            'is_active',
            'sort_order',
        ]);

        $attributes['slug'] = filled($attributes['slug'] ?? null)
            ? $attributes['slug']
            : SlugGenerator::generate($attributes['name'], Category::class, $category?->id);

        if (array_key_exists('is_active', $attributes)) {
            $attributes['is_active'] = (bool) $attributes['is_active'];
        }

        if (array_key_exists('sort_order', $attributes)) {
            $attributes['sort_order'] = (int) $attributes['sort_order'];
        }

        return $attributes;
    }
}
