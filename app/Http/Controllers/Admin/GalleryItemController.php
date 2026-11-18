<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GalleryItemRequest;
use App\Models\GalleryItem;
use App\Services\Content\MediaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class GalleryItemController extends Controller
{
    public function __construct(private readonly MediaService $media) {}

    public function index(): View
    {
        $this->authorize('viewAny', GalleryItem::class);

        return view('admin.content.gallery.index', [
            'items' => GalleryItem::ordered()->paginate(15)->withQueryString(),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', GalleryItem::class);

        return view('admin.content.gallery.form', [
            'item' => new GalleryItem,
            'action' => route('admin.gallery.store'),
            'method' => 'POST',
            'heading' => 'Create gallery item',
        ]);
    }

    public function store(GalleryItemRequest $request): RedirectResponse
    {
        $this->authorize('create', GalleryItem::class);

        $data = $request->validated();
        $image = $data['image'];
        unset($data['image']);

        $data['image_path'] = $this->media->store($image, 'gallery');
        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = $data['sort_order'] ?? 0;

        GalleryItem::create($data);

        return redirect()->route('admin.gallery.index')->with('status', 'Gallery item created.');
    }

    public function edit(GalleryItem $galleryItem): View
    {
        $this->authorize('update', $galleryItem);

        return view('admin.content.gallery.form', [
            'item' => $galleryItem,
            'action' => route('admin.gallery.update', $galleryItem),
            'method' => 'PUT',
            'heading' => 'Edit gallery item',
        ]);
    }

    public function update(GalleryItemRequest $request, GalleryItem $galleryItem): RedirectResponse
    {
        $this->authorize('update', $galleryItem);

        $data = $request->validated();
        $image = $data['image'] ?? null;
        unset($data['image']);

        $data['image_path'] = $this->media->store($image, 'gallery', $galleryItem->image_path);
        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = $data['sort_order'] ?? 0;

        $galleryItem->update($data);

        return redirect()->route('admin.gallery.index')->with('status', 'Gallery item updated.');
    }

    public function destroy(GalleryItem $galleryItem): RedirectResponse
    {
        $this->authorize('delete', $galleryItem);

        $this->media->delete($galleryItem->image_path);
        $galleryItem->delete();

        return redirect()->route('admin.gallery.index')->with('status', 'Gallery item deleted.');
    }
}
