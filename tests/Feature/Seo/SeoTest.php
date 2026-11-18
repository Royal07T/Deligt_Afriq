<?php

namespace Tests\Feature\Seo;

use App\Models\ContentPage;
use App\Models\Product;
use App\Support\Catalogue\ProductStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoTest extends TestCase
{
    use RefreshDatabase;

    public function test_sitemap_includes_public_content_and_excludes_drafts(): void
    {
        Product::factory()->create([
            'name' => 'Sitemap Gown',
            'slug' => 'sitemap-gown',
            'status' => ProductStatus::ACTIVE,
        ]);

        ContentPage::create([
            'title' => 'Published Page',
            'slug' => 'published-page',
            'body' => 'Public body.',
            'is_published' => true,
            'published_at' => now(),
        ]);

        ContentPage::create([
            'title' => 'Draft Page',
            'slug' => 'draft-page',
            'body' => 'Private body.',
        ]);

        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertSee('<urlset', false)
            ->assertSee(route('products.show', 'sitemap-gown'))
            ->assertSee(route('pages.show', 'published-page'))
            ->assertDontSee('draft-page');
    }

    public function test_dynamic_robots_route_is_served(): void
    {
        $this->assertFileDoesNotExist(public_path('robots.txt'));

        $this->get('/robots.txt')
            ->assertOk()
            ->assertSee('User-agent: *')
            ->assertSee('Disallow: /admin')
            ->assertSee('Sitemap: '.url('/sitemap.xml'));
    }

    public function test_storefront_uses_page_seo_metadata(): void
    {
        $page = ContentPage::create([
            'title' => 'Delivery Information',
            'slug' => 'delivery-information',
            'body' => 'Delivery body.',
            'meta_title' => 'Delivery Details | Delight Afriq',
            'meta_description' => 'Delivery timelines and charges.',
            'is_published' => true,
            'published_at' => now(),
        ]);

        $this->get(route('pages.show', $page))
            ->assertOk()
            ->assertSee('<title>Delivery Details | Delight Afriq</title>', false)
            ->assertSee('content="Delivery timelines and charges."', false)
            ->assertSee('<link rel="canonical" href="'.route('pages.show', $page).'">', false)
            ->assertSee('property="og:image"', false);
    }
}
