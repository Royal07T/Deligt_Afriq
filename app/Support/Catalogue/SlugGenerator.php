<?php

namespace App\Support\Catalogue;

use Illuminate\Support\Str;

final class SlugGenerator
{
    public static function generate(string $name, string $modelClass, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'item';
        $query = $modelClass::query();

        if (method_exists($modelClass, 'withTrashed')) {
            $query->withTrashed();
        }

        if ($ignoreId !== null) {
            $query->whereKeyNot($ignoreId);
        }

        $slug = $base;
        $counter = 2;

        while ((clone $query)->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$counter;
            $counter++;
        }

        return $slug;
    }
}
