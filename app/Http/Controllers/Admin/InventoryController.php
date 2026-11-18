<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateInventoryRequest;
use App\Models\Inventory;
use App\Models\Product;
use App\Services\Catalogue\InventoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class InventoryController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Inventory::class);

        $products = Product::query()
            ->with(['category', 'inventory'])
            ->whereHas('inventory')
            ->when(request()->boolean('low'), function ($query): void {
                $query->where('inventories.track_inventory', true)
                    ->whereColumn('inventories.quantity', '<=', DB::raw('inventories.low_stock_threshold + inventories.reserved'));
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.inventory.index', ['products' => $products]);
    }

    public function edit(Product $product): View
    {
        $this->authorize('manageInventory', $product);

        $inventory = $product->inventory()->firstOrCreate([], [
            'quantity' => 0,
            'reserved' => 0,
            'low_stock_threshold' => 5,
            'track_inventory' => true,
        ]);

        return view('admin.inventory.edit', [
            'product' => $product,
            'inventory' => $inventory,
        ]);
    }

    public function update(UpdateInventoryRequest $request, Product $product, InventoryService $service): RedirectResponse
    {
        $this->authorize('manageInventory', $product);

        $service->adjust($request->user(), $product, $request->validated());

        return redirect()->route('admin.inventory.edit', $product)->with('status', 'Inventory updated.');
    }
}
