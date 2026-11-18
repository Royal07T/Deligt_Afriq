<?php

namespace App\Services\Catalogue;

use App\Models\Collection;
use App\Support\Catalogue\SlugGenerator;
use Illuminate\Support\Arr;

class CollectionService
{
    public function create(array $data): Collection
    {
        return Collection::create($this->attributes($data));
    }

    public function update(Collection $collection, array $data): Collection
    {
        $collection->update($this->attributes($data, $collection));

        return $collection->fresh();
    }

    public function delete(Collection $collection): void
    {
        $collection->delete();
    }

    private function attributes(array $data, ?Collection $collection = null): array
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
            : SlugGenerator::generate($attributes['name'], Collection::class, $collection?->id);

        if (array_key_exists('is_active', $attributes)) {
            $attributes['is_active'] = (bool) $attributes['is_active'];
        }

        if (array_key_exists('sort_order', $attributes)) {
            $attributes['sort_order'] = (int) $attributes['sort_order'];
        }

        return $attributes;
    }
}
