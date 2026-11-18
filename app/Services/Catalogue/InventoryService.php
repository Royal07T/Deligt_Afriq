<?php

namespace App\Services\Catalogue;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\User;
use App\Services\Access\AuditService;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;

class InventoryService
{
    public function __construct(private readonly AuditService $audit) {}

    public function adjust(User $actor, Product $product, array $data): Inventory
    {
        $inventory = $product->inventory()->firstOrCreate([], [
            'quantity' => 0,
            'reserved' => 0,
            'low_stock_threshold' => 5,
            'track_inventory' => true,
        ]);

        $hasSetQuantity = array_key_exists('quantity', $data) && $data['quantity'] !== null && $data['quantity'] !== '';
        $quantity = $hasSetQuantity
            ? (int) $data['quantity']
            : $inventory->quantity + (int) ($data['adjustment'] ?? 0);

        if ($quantity < 0) {
            throw ValidationException::withMessages([
                'quantity' => 'Stock quantity cannot be negative.',
            ]);
        }

        $attributes = ['quantity' => $quantity];

        foreach (['reserved', 'low_stock_threshold'] as $field) {
            if (array_key_exists($field, $data) && $data[$field] !== null && $data[$field] !== '') {
                $attributes[$field] = (int) $data[$field];
            }
        }

        if (array_key_exists('track_inventory', $data)) {
            $attributes['track_inventory'] = (bool) $data['track_inventory'];
        }

        $reserved = $attributes['reserved'] ?? $inventory->reserved;

        if ($reserved > $quantity) {
            throw ValidationException::withMessages([
                'reserved' => 'Reserved stock cannot exceed stock on hand.',
            ]);
        }

        $inventory->update(Arr::only($attributes, [
            'quantity',
            'reserved',
            'low_stock_threshold',
            'track_inventory',
        ]));

        $this->audit->record(
            'inventory.adjusted',
            $actor,
            Product::class,
            $product->id,
            Arr::only($attributes, ['quantity', 'reserved', 'low_stock_threshold']),
        );

        return $inventory->fresh();
    }
}
