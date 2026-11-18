<?php

namespace Tests\Feature\Content;

use App\Models\Banner;
use App\Models\ContentPage;
use App\Models\GalleryItem;
use App\Models\User;
use App\Support\Access\Roles;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ContentManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedAccessControl();
    }

    public function test_super_admin_can_create_update_and_delete_pages(): void
    {
        $admin = $this->superAdmin();

        $this->actingAs($admin)
            ->post(route('admin.pages.store'), [
                'title' => 'Our Story',
                'body' => "First paragraph.\n\nSecond paragraph.",
                'is_published' => true,
            ])
            ->assertRedirect(route('admin.pages.index'));

        $page = ContentPage::where('slug', 'our-story')->firstOrFail();
        $this->assertNotNull($page->published_at);

        $this->actingAs($admin)
            ->put(route('admin.pages.update', $page), [
                'title' => 'Our Heritage',
                'slug' => 'our-heritage',
                'body' => 'Updated body.',
                'is_published' => true,
            ])
            ->assertRedirect(route('admin.pages.index'));

        $this->assertDatabaseHas('content_pages', [
            'id' => $page->id,
            'title' => 'Our Heritage',
            'slug' => 'our-heritage',
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.pages.destroy', $page))
            ->assertRedirect(route('admin.pages.index'));

        $this->assertDatabaseMissing('content_pages', ['id' => $page->id]);
    }

    public function test_staff_without_content_permission_cannot_manage_content(): void
    {
        $staff = User::factory()->create();
        $staff->assignRole(Roles::ORDER_PROCESSING);

        $this->actingAs($staff)
            ->get(route('admin.pages.index'))
            ->assertForbidden();

        $this->actingAs($staff)
            ->get(route('admin.banners.index'))
            ->assertForbidden();

        $this->actingAs($staff)
            ->get(route('admin.homepage-sections.index'))
            ->assertForbidden();

        $this->actingAs($staff)
            ->get(route('admin.gallery.index'))
            ->assertForbidden();

        $this->actingAs($staff)
            ->get(route('admin.newsletter.index'))
            ->assertForbidden();
    }

    public function test_banner_and_gallery_images_are_stored_on_upload(): void
    {
        Storage::fake('public');

        $admin = $this->superAdmin();
        $image = fn (string $name): UploadedFile => UploadedFile::fake()->createWithContent(
            $name,
            file_get_contents(public_path('media/images/delight-logo.jpeg')),
        );

        $this->actingAs($admin)
            ->post(route('admin.banners.store'), [
                'title' => 'Launch banner',
                'subtitle' => 'New collection',
                'cta_label' => 'Shop now',
                'cta_url' => route('catalogue.index'),
                'image' => $image('banner.jpeg'),
                'sort_order' => 1,
                'is_active' => true,
            ])
            ->assertRedirect(route('admin.banners.index'));

        $banner = Banner::where('title', 'Launch banner')->firstOrFail();
        $this->assertStringStartsWith('banners/', $banner->image_path);
        Storage::disk('public')->assertExists($banner->image_path);

        $this->actingAs($admin)
            ->post(route('admin.gallery.store'), [
                'title' => 'Studio image',
                'category' => 'Studio',
                'image' => $image('gallery.jpeg'),
                'sort_order' => 1,
                'is_active' => true,
            ])
            ->assertRedirect(route('admin.gallery.index'));

        $galleryItem = GalleryItem::where('title', 'Studio image')->firstOrFail();
        $this->assertStringStartsWith('gallery/', $galleryItem->image_path);
        Storage::disk('public')->assertExists($galleryItem->image_path);
    }

    public function test_homepage_sections_can_be_created_and_rendered(): void
    {
        $admin = $this->superAdmin();

        $this->actingAs($admin)
            ->post(route('admin.homepage-sections.store'), [
                'title' => 'Made in Abuja',
                'key' => 'made-in-abuja',
                'body' => 'Designed and crafted with intention.',
                'sort_order' => 1,
                'is_active' => true,
            ])
            ->assertRedirect(route('admin.homepage-sections.index'));

        $this->assertDatabaseHas('homepage_sections', [
            'key' => 'made-in-abuja',
            'title' => 'Made in Abuja',
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Made in Abuja')
            ->assertSee('Designed and crafted with intention.');
    }

    public function test_storefront_shows_published_pages_only_and_escapes_body(): void
    {
        $published = ContentPage::create([
            'title' => 'Client Care',
            'slug' => 'client-care',
            'body' => 'Safe <script>alert(1)</script> text',
            'excerpt' => 'How we care for our clients.',
            'is_published' => true,
            'published_at' => now(),
        ]);

        $draft = ContentPage::create([
            'title' => 'Secret Draft',
            'slug' => 'secret-draft',
            'body' => 'Not ready.',
        ]);

        $this->get(route('pages.show', $published))
            ->assertOk()
            ->assertSee('Client Care')
            ->assertSee('How we care for our clients.')
            ->assertSee('Safe')
            ->assertDontSee('<script>alert(1)</script>', false);

        $this->get(route('pages.show', $draft))->assertNotFound();
    }

    private function superAdmin(): User
    {
        $user = User::factory()->create();
        $user->assignRole(Roles::SUPER_ADMIN);

        return $user;
    }
}
