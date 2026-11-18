<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ContentPageRequest;
use App\Models\ContentPage;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ContentPageController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', ContentPage::class);

        return view('admin.content.pages.index', [
            'pages' => ContentPage::latest()->paginate(15)->withQueryString(),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', ContentPage::class);

        return view('admin.content.pages.form', [
            'page' => new ContentPage,
            'action' => route('admin.pages.store'),
            'method' => 'POST',
            'heading' => 'Create page',
        ]);
    }

    public function store(ContentPageRequest $request): RedirectResponse
    {
        $this->authorize('create', ContentPage::class);

        $data = $request->validated();
        $data['is_published'] = $request->boolean('is_published');
        $data['published_at'] = $data['is_published'] ? ($data['published_at'] ?? now()) : null;

        ContentPage::create($data);

        return redirect()->route('admin.pages.index')->with('status', 'Page created.');
    }

    public function edit(ContentPage $page): View
    {
        $this->authorize('update', $page);

        return view('admin.content.pages.form', [
            'page' => $page,
            'action' => route('admin.pages.update', $page),
            'method' => 'PUT',
            'heading' => 'Edit page',
        ]);
    }

    public function update(ContentPageRequest $request, ContentPage $page): RedirectResponse
    {
        $this->authorize('update', $page);

        $data = $request->validated();
        $data['is_published'] = $request->boolean('is_published');
        $data['published_at'] = $data['is_published'] ? ($data['published_at'] ?? $page->published_at ?? now()) : null;

        $page->update($data);

        return redirect()->route('admin.pages.index')->with('status', 'Page updated.');
    }

    public function destroy(ContentPage $page): RedirectResponse
    {
        $this->authorize('delete', $page);

        $page->delete();

        return redirect()->route('admin.pages.index')->with('status', 'Page deleted.');
    }
}
