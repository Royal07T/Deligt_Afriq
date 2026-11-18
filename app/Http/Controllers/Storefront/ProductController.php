<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function show(string $slug): View
    {
        $product = Product::query()
            ->active()
            ->with(['category', 'collections', 'images', 'inventory'])
            ->where('slug', $slug)
            ->firstOrFail();

        $related = Product::query()
            ->active()
            ->with('primaryImage')
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->limit(4)
            ->get();

        return view('storefront.products.show', [
            'product' => $product,
            'related' => $related,
            'metaTitle' => $product->name.config('seo.title_suffix'),
            'metaDescription' => $product->short_description ?: config('seo.description'),
            'ogImage' => $product->primaryImage?->url ?: asset(config('seo.image')),
            'canonical' => route('products.show', $product->slug),
        ]);
    }
}
