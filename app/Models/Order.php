<?php

namespace App\Models;

use App\Support\Commerce\Money;
use App\Support\Orders\OrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'user_id',
        'status',
        'payment_method',
        'payment_status',
        'payment_reference',
        'currency',
        'subtotal',
        'shipping',
        'discount',
        'total',
        'shipping_name',
        'shipping_phone',
        'shipping_email',
        'shipping_line_1',
        'shipping_line_2',
        'shipping_city',
        'shipping_state',
        'shipping_postal_code',
        'shipping_country',
        'notes',
        'placed_at',
        'paid_at',
        'inventory_released_at',
    ];

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'subtotal' => 'decimal:2',
            'shipping' => 'decimal:2',
            'discount' => 'decimal:2',
            'total' => 'decimal:2',
            'placed_at' => 'datetime',
            'paid_at' => 'datetime',
            'inventory_released_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function paymentTransactions(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    public function itemCount(): int
    {
        return (int) $this->items->sum('quantity');
    }

    public function subtotalMinor(): int
    {
        return Money::toMinor((string) $this->subtotal);
    }

    public function shippingMinor(): int
    {
        return Money::toMinor((string) $this->shipping);
    }

    public function discountMinor(): int
    {
        return Money::toMinor((string) $this->discount);
    }

    public function totalMinor(): int
    {
        return Money::toMinor((string) $this->total);
    }

    public function formattedTotal(): string
    {
        return Money::format($this->totalMinor(), $this->currency);
    }

    public function canBeCancelledByCustomer(): bool
    {
        return in_array($this->status, [OrderStatus::PENDING, OrderStatus::AWAITING_PAYMENT], true);
    }
}
