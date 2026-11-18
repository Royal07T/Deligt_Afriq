<?php

namespace Tests\Feature\Catalogue;

use App\Models\Category;
use App\Models\Collection;
use App\Models\Product;
use App\Support\Catalogue\ProductStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogueTest extends TestCase
{
    use RefreshDatabase;

    public function test_catalogue_lists_only_active_products(): void
    {
        $category = Category::factory()->create(['name' => 'Visible Category']);

        Product::factory()->create([
            'category_id' => $category->id,
            'name' => 'Visible Gown',
            'status' => ProductStatus::ACTIVE,
        ]);
        Product::factory()->create([
            'category_id' => $category->id,
            'name' => 'Hidden Draft Gown',
            'status' => ProductStatus::DRAFT,
        ]);

        $this->get(route('catalogue.index'))
            ->assertOk()
            ->assertSee('Visible Gown')
            ->assertDontSee('Hidden Draft Gown');
    }

    public function test_catalogue_can_filter_by_category_collection_and_search(): void
    {
        $includedCategory = Category::factory()->create(['slug' => 'included-category']);
        $excludedCategory = Category::factory()->create(['slug' => 'excluded-category']);
        $collection = Collection::factory()->create(['slug' => 'occasion-edit']);

        $included = Product::factory()->create([
            'category_id' => $includedCategory->id,
            'name' => 'Golden Glow Dress',
        ]);
        $included->collections()->attach($collection);

        Product::factory()->create([
            'category_id' => $excludedCategory->id,
            'name' => 'Everyday Shirt',
        ]);

        $this->get(route('catalogue.index', ['category' => 'included-category']))
            ->assertOk()
            ->assertSee('Golden Glow Dress')
            ->assertDontSee('Everyday Shirt');

        $this->get(route('catalogue.index', ['collection' => 'occasion-edit']))
            ->assertOk()
            ->assertSee('Golden Glow Dress')
            ->assertDontSee('Everyday Shirt');

        $this->get(route('catalogue.index', ['search' => 'Golden']))
            ->assertOk()
            ->assertSee('Golden Glow Dress')
            ->assertDontSee('Everyday Shirt');
    }

    public function test_catalogue_can_sort_by_price(): void
    {
        $category = Category::factory()->create();

        Product::factory()->create([
            'category_id' => $category->id,
            'name' => 'Expensive Piece',
            'price' => 90000,
        ]);
        Product::factory()->create([
            'category_id' => $category->id,
            'name' => 'Affordable Piece',
            'price' => 20000,
        ]);

        $this->get(route('catalogue.index', ['sort' => 'price_asc']))
            ->assertOk()
            ->assertSeeInOrder(['Affordable Piece', 'Expensive Piece']);
    }

    public function test_product_detail_page_shows_active_product(): void
    {
        $product = Product::factory()->create([
            'name' => 'Statement Ankara Gown',
            'short_description' => 'A striking gown for special occasions.',
            'price' => 150000,
        ]);

        $this->get(route('products.show', $product))
            ->assertOk()
            ->assertSee('Statement Ankara Gown')
            ->assertSee('150,000')
            ->assertSee('Inquire on WhatsApp');
    }

    public function test_draft_product_detail_returns_not_found(): void
    {
        $product = Product::factory()->create(['status' => ProductStatus::DRAFT]);

        $this->get(route('products.show', $product))->assertNotFound();
    }

    public function test_out_of_stock_product_is_marked_on_detail_page(): void
    {
        $product = Product::factory()->create(['name' => 'Sold Out Piece']);
        $product->inventory()->update(['quantity' => 0]);

        $this->get(route('products.show', $product))
            ->assertOk()
            ->assertSee('Out of stock');
    }
}
