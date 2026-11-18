<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductRequest;
use App\Models\Category;
use App\Models\Collection;
use App\Models\Product;
use App\Services\Catalogue\ProductService;
use App\Support\Catalogue\ProductStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Product::class);

        $products = Product::query()
            ->with(['category', 'inventory', 'primaryImage'])
            ->when($request->query('search'), function ($query, string $search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%");
                });
            })
            ->when($request->query('category'), fn ($query, $category) => $query->where('category_id', (int) $category))
            ->when($request->query('status'), fn ($query, $status) => $query->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.products.index', [
            'products' => $products,
            'categories' => Category::ordered()->get(),
            'statuses' => ProductStatus::ALL,
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Product::class);

        return view('admin.products.create', [
            'product' => new Product,
            'categories' => Category::active()->ordered()->get(),
            'collections' => Collection::active()->ordered()->get(),
            'statuses' => ProductStatus::ALL,
        ]);
    }

    public function store(ProductRequest $request, ProductService $service): RedirectResponse
    {
        $this->authorize('create', Product::class);

        $product = $service->create(
            $request->user(),
            $request->validated(),
            $request->file('images', []),
        );

        return redirect()->route('admin.products.edit', $product)->with('status', 'Product created.');
    }

    public function edit(Product $product): View
    {
        $this->authorize('update', $product);

        return view('admin.products.edit', [
            'product' => $product->load(['images', 'collections', 'inventory']),
            'categories' => Category::active()->ordered()->get(),
            'collections' => Collection::active()->ordered()->get(),
            'statuses' => ProductStatus::ALL,
        ]);
    }

    public function update(ProductRequest $request, Product $product, ProductService $service): RedirectResponse
    {
        $this->authorize('update', $product);

        $service->update(
            $request->user(),
            $product,
            $request->validated(),
            $request->file('images', []),
        );

        return redirect()->route('admin.products.edit', $product)->with('status', 'Product updated.');
    }

    public function destroy(Request $request, Product $product, ProductService $service): RedirectResponse
    {
        $this->authorize('delete', $product);

        $service->delete($request->user(), $product);

        return redirect()->route('admin.products.index')->with('status', 'Product deleted.');
    }
}
