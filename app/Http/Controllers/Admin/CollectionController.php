<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CollectionRequest;
use App\Models\Collection;
use App\Services\Catalogue\CollectionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CollectionController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Collection::class);

        return view('admin.collections.index', [
            'collections' => Collection::withCount('products')->latest()->paginate(15)->withQueryString(),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Collection::class);

        return view('admin.collections.create', ['collection' => new Collection]);
    }

    public function store(CollectionRequest $request, CollectionService $service): RedirectResponse
    {
        $this->authorize('create', Collection::class);

        $service->create($request->validated());

        return redirect()->route('admin.collections.index')->with('status', 'Collection created.');
    }

    public function edit(Collection $collection): View
    {
        $this->authorize('update', $collection);

        return view('admin.collections.edit', ['collection' => $collection]);
    }

    public function update(CollectionRequest $request, Collection $collection, CollectionService $service): RedirectResponse
    {
        $this->authorize('update', $collection);

        $service->update($collection, $request->validated());

        return redirect()->route('admin.collections.index')->with('status', 'Collection updated.');
    }

    public function destroy(Collection $collection, CollectionService $service): RedirectResponse
    {
        $this->authorize('delete', $collection);

        $service->delete($collection);

        return redirect()->route('admin.collections.index')->with('status', 'Collection deleted.');
    }
}
