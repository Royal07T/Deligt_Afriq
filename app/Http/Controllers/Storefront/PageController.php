<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\ContentPage;
use Illuminate\View\View;

class PageController extends Controller
{
    public function show(ContentPage $page): View
    {
        abort_unless($page->is_published, 404);

        return view('storefront.pages.show', [
            'page' => $page,
            'metaTitle' => $page->meta_title ?: $page->title.config('seo.title_suffix'),
            'metaDescription' => $page->meta_description ?: $page->excerpt ?: config('seo.description'),
            'canonical' => route('pages.show', $page),
        ]);
    }
}
