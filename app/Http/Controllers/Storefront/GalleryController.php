<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\GalleryItem;
use Illuminate\View\View;

class GalleryController extends Controller
{
    public function index(): View
    {
        return view('storefront.gallery.index', [
            'items' => GalleryItem::active()->ordered()->get(),
            'metaTitle' => 'The Delight Gallery'.config('seo.title_suffix'),
            'metaDescription' => 'Explore recent Delight Afriq designs, accessories and Studio moments from our Abuja atelier.',
            'canonical' => route('gallery.index'),
        ]);
    }
}
