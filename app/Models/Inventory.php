<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'quantity',
        'reserved',
        'low_stock_threshold',
        'track_inventory',
    ];

    protected function casts(): array
    {
        return [
            'product_id' => 'integer',
            'quantity' => 'integer',
            'reserved' => 'integer',
            'low_stock_threshold' => 'integer',
            'track_inventory' => 'boolean',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function availableQuantity(): int
    {
        return max(0, $this->quantity - $this->reserved);
    }

    public function isInStock(): bool
    {
        return ! $this->track_inventory || $this->availableQuantity() > 0;
    }

    public function isLow(): bool
    {
        return $this->track_inventory && $this->availableQuantity() <= $this->low_stock_threshold;
    }
}
