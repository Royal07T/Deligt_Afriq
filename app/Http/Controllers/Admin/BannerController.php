<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BannerRequest;
use App\Models\Banner;
use App\Services\Content\MediaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BannerController extends Controller
{
    public function __construct(private readonly MediaService $media) {}

    public function index(): View
    {
        $this->authorize('viewAny', Banner::class);

        return view('admin.content.banners.index', [
            'banners' => Banner::ordered()->paginate(15)->withQueryString(),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Banner::class);

        return view('admin.content.banners.form', [
            'banner' => new Banner,
            'action' => route('admin.banners.store'),
            'method' => 'POST',
            'heading' => 'Create banner',
        ]);
    }

    public function store(BannerRequest $request): RedirectResponse
    {
        $this->authorize('create', Banner::class);

        $data = $request->validated();
        $image = $data['image'];
        unset($data['image']);

        $data['image_path'] = $this->media->store($image, 'banners');
        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = $data['sort_order'] ?? 0;

        Banner::create($data);

        return redirect()->route('admin.banners.index')->with('status', 'Banner created.');
    }

    public function edit(Banner $banner): View
    {
        $this->authorize('update', $banner);

        return view('admin.content.banners.form', [
            'banner' => $banner,
            'action' => route('admin.banners.update', $banner),
            'method' => 'PUT',
            'heading' => 'Edit banner',
        ]);
    }

    public function update(BannerRequest $request, Banner $banner): RedirectResponse
    {
        $this->authorize('update', $banner);

        $data = $request->validated();
        $image = $data['image'] ?? null;
        unset($data['image']);

        $data['image_path'] = $this->media->store($image, 'banners', $banner->image_path);
        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = $data['sort_order'] ?? 0;

        $banner->update($data);

        return redirect()->route('admin.banners.index')->with('status', 'Banner updated.');
    }

    public function destroy(Banner $banner): RedirectResponse
    {
        $this->authorize('delete', $banner);

        $this->media->delete($banner->image_path);
        $banner->delete();

        return redirect()->route('admin.banners.index')->with('status', 'Banner deleted.');
    }
}
