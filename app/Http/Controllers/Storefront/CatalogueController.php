<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Collection;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CatalogueController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $categorySlug = (string) $request->query('category', '');
        $collectionSlug = (string) $request->query('collection', '');
        $sort = (string) $request->query('sort', '');

        $products = Product::query()
            ->active()
            ->with(['category', 'primaryImage', 'inventory'])
            ->when($search !== '', fn ($query) => $query->where(function ($query) use ($search): void {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('short_description', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            }))
            ->when($categorySlug !== '', fn ($query) => $query->whereHas(
                'category',
                fn ($query) => $query->where('slug', $categorySlug),
            ))
            ->when($collectionSlug !== '', fn ($query) => $query->whereHas(
                'collections',
                fn ($query) => $query->where('slug', $collectionSlug),
            ))
            ->tap(function ($query) use ($sort): void {
                match ($sort) {
                    'price_asc' => $query->orderBy('price'),
                    'price_desc' => $query->orderByDesc('price'),
                    'name' => $query->orderBy('name'),
                    default => $query->orderByDesc('is_featured')->orderByDesc('created_at'),
                };
            })
            ->paginate(12)
            ->withQueryString();

        return view('storefront.catalogue.index', [
            'products' => $products,
            'categories' => Category::active()->ordered()->get(),
            'collections' => Collection::active()->ordered()->get(),
            'filters' => [
                'search' => $search,
                'category' => $categorySlug,
                'collection' => $collectionSlug,
                'sort' => $sort,
            ],
            'metaTitle' => 'Shop the Collection'.config('seo.title_suffix'),
            'metaDescription' => 'Browse bespoke fashion, ready-to-wear pieces and statement accessories from Delight Afriq in Abuja, Nigeria.',
            'canonical' => route('catalogue.index'),
        ]);
    }
}
