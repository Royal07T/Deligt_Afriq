<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Collection;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\User;
use App\Support\Access\Roles;
use App\Support\Catalogue\ProductStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CatalogueManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedAccessControl();
    }

    public function test_staff_without_catalogue_permission_cannot_view_products(): void
    {
        $staff = User::factory()->create();
        $staff->assignRole(Roles::ORDER_PROCESSING);

        $this->actingAs($staff)->get(route('admin.products.index'))->assertForbidden();
    }

    public function test_super_admin_can_create_category_collection_and_product_with_image(): void
    {
        Storage::fake('public');

        $admin = $this->superAdmin();
        $category = Category::factory()->create();
        $collection = Collection::factory()->create();

        $imageContent = file_get_contents(public_path('media/images/delight-logo.jpeg'));

        $this->actingAs($admin)
            ->post(route('admin.products.store'), [
                'category_id' => $category->id,
                'name' => 'Heritage Gown',
                'slug' => '',
                'sku' => 'DA-TEST-001',
                'short_description' => 'A test gown.',
                'description' => 'A test gown created through the admin catalogue.',
                'price' => 120000,
                'compare_at_price' => null,
                'currency' => 'ngn',
                'status' => ProductStatus::ACTIVE,
                'is_featured' => 1,
                'collections' => [$collection->id],
                'inventory' => [
                    'quantity' => 7,
                    'reserved' => 1,
                    'low_stock_threshold' => 3,
                    'track_inventory' => 1,
                ],
                'images' => [
                    UploadedFile::fake()->createWithContent('gown.jpeg', $imageContent),
                ],
            ])
            ->assertRedirect();

        $product = Product::where('sku', 'DA-TEST-001')->firstOrFail();

        $this->assertSame('heritage-gown', $product->slug);
        $this->assertSame('NGN', $product->currency);
        $this->assertTrue($product->is_featured);
        $this->assertDatabaseHas('inventories', [
            'product_id' => $product->id,
            'quantity' => 7,
            'reserved' => 1,
            'low_stock_threshold' => 3,
        ]);
        $this->assertCount(1, $product->images);
        $this->assertTrue($product->images->first()->is_primary);
        $this->assertTrue($product->collections->contains($collection));
        $this->assertDatabaseHas('activity_logs', [
            'action' => 'product.created',
            'user_id' => $admin->id,
            'subject_id' => $product->id,
        ]);
        Storage::disk('public')->assertExists($product->images->first()->path);
    }

    public function test_product_sku_and_slug_must_be_unique(): void
    {
        $category = Category::factory()->create();
        $existing = Product::factory()->create([
            'category_id' => $category->id,
            'sku' => 'DA-DUPLICATE-001',
            'slug' => 'duplicate-piece',
        ]);

        $this->actingAs($this->superAdmin())
            ->post(route('admin.products.store'), [
                'category_id' => $category->id,
                'name' => 'Another Piece',
                'slug' => $existing->slug,
                'sku' => $existing->sku,
                'price' => 50000,
                'currency' => 'NGN',
                'status' => ProductStatus::DRAFT,
                'inventory' => ['quantity' => 1],
            ])
            ->assertSessionHasErrors(['sku', 'slug']);
    }

    public function test_super_admin_can_update_and_delete_product(): void
    {
        $product = Product::factory()->create(['name' => 'Old Name']);

        $this->actingAs($this->superAdmin())
            ->put(route('admin.products.update', $product), [
                'category_id' => $product->category_id,
                'name' => 'New Name',
                'sku' => $product->sku,
                'price' => 75000,
                'currency' => 'NGN',
                'status' => ProductStatus::ACTIVE,
                'inventory' => ['quantity' => 12],
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'New Name',
            'price' => 75000,
        ]);

        $this->actingAs($this->superAdmin())
            ->delete(route('admin.products.destroy', $product))
            ->assertRedirect();

        $this->assertSoftDeleted('products', ['id' => $product->id]);
    }

    public function test_uploading_product_image_preserves_single_primary_image(): void
    {
        $product = Product::factory()->create();
        $primaryImage = ProductImage::factory()->create([
            'product_id' => $product->id,
            'is_primary' => true,
        ]);

        $this->actingAs($this->superAdmin())
            ->put(route('admin.products.update', $product), [
                'category_id' => $product->category_id,
                'name' => $product->name,
                'slug' => $product->slug,
                'sku' => $product->sku,
                'price' => $product->price,
                'currency' => $product->currency,
                'status' => $product->status,
                'inventory' => ['quantity' => 10],
                'images' => [
                    UploadedFile::fake()->createWithContent(
                        'extra.jpeg',
                        file_get_contents(public_path('media/images/delight-logo.jpeg'))
                    ),
                ],
            ])
            ->assertRedirect();

        $this->assertSame(1, $product->images()->where('is_primary', true)->count());
        $this->assertTrue($product->images()->whereKey($primaryImage->id)->first()->is_primary);
    }

    public function test_category_with_products_cannot_be_deleted(): void
    {
        $category = Category::factory()->create();
        Product::factory()->create(['category_id' => $category->id]);

        $this->actingAs($this->superAdmin())
            ->delete(route('admin.categories.destroy', $category))
            ->assertSessionHasErrors('category');

        $this->assertNotSoftDeleted('categories', ['id' => $category->id]);
    }

    public function test_inventory_can_be_adjusted_and_low_stock_detected(): void
    {
        $product = Product::factory()->create();
        $admin = $this->superAdmin();

        $this->actingAs($admin)
            ->put(route('admin.inventory.update', $product), [
                'quantity' => 3,
                'reserved' => 1,
                'low_stock_threshold' => 5,
                'track_inventory' => 1,
            ])
            ->assertRedirect();

        $inventory = $product->inventory()->first();

        $this->assertSame(3, $inventory->quantity);
        $this->assertSame(2, $inventory->availableQuantity());
        $this->assertTrue($inventory->isLow());
        $this->assertDatabaseHas('activity_logs', [
            'action' => 'inventory.adjusted',
            'user_id' => $admin->id,
            'subject_id' => $product->id,
        ]);
    }

    public function test_inventory_cannot_be_adjusted_to_negative_quantity(): void
    {
        $product = Product::factory()->create();

        $this->actingAs($this->superAdmin())
            ->put(route('admin.inventory.update', $product), ['adjustment' => -1000])
            ->assertSessionHasErrors('quantity');

        $this->assertSame(10, $product->inventory()->first()->quantity);
    }

    private function superAdmin(): User
    {
        $user = User::factory()->create();
        $user->assignRole(Roles::SUPER_ADMIN);

        return $user;
    }
}
