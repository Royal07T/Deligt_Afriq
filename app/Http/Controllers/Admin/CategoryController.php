<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CategoryRequest;
use App\Models\Category;
use App\Services\Catalogue\CategoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Category::class);

        return view('admin.categories.index', [
            'categories' => Category::withCount('products')->latest()->paginate(15)->withQueryString(),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Category::class);

        return view('admin.categories.create', ['category' => new Category]);
    }

    public function store(CategoryRequest $request, CategoryService $service): RedirectResponse
    {
        $this->authorize('create', Category::class);

        $service->create($request->validated());

        return redirect()->route('admin.categories.index')->with('status', 'Category created.');
    }

    public function edit(Category $category): View
    {
        $this->authorize('update', $category);

        return view('admin.categories.edit', ['category' => $category]);
    }

    public function update(CategoryRequest $request, Category $category, CategoryService $service): RedirectResponse
    {
        $this->authorize('update', $category);

        $service->update($category, $request->validated());

        return redirect()->route('admin.categories.index')->with('status', 'Category updated.');
    }

    public function destroy(Category $category, CategoryService $service): RedirectResponse
    {
        $this->authorize('delete', $category);

        $service->delete($category);

        return redirect()->route('admin.categories.index')->with('status', 'Category deleted.');
    }
}
