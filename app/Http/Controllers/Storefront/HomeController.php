<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Category;
use App\Models\GalleryItem;
use App\Models\HomepageSection;
use App\Models\Product;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        return view('storefront.home', [
            'featuredProducts' => Product::query()
                ->active()
                ->featured()
                ->with(['primaryImage', 'category', 'inventory'])
                ->limit(4)
                ->get(),
            'categories' => Category::active()->ordered()->limit(4)->get(),
            'banners' => Banner::active()->ordered()->limit(4)->get(),
            'sections' => HomepageSection::active()->ordered()->get(),
            'galleryPreview' => GalleryItem::active()->ordered()->limit(4)->get(),
            'metaTitle' => config('seo.title'),
            'metaDescription' => config('seo.description'),
            'canonical' => route('home'),
        ]);
    }
}
