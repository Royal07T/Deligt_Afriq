<?php

namespace App\Services\Commerce;

use App\Models\Address;
use App\Models\Cart;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\Access\AuditService;
use App\Support\Catalogue\ProductStatus;
use App\Support\Commerce\Money;
use App\Support\Commerce\ShippingCalculator;
use App\Support\Orders\OrderNumberGenerator;
use App\Support\Orders\OrderStatus;
use App\Support\Orders\PaymentMethod;
use App\Support\Orders\PaymentStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class OrderService
{
    public function __construct(private readonly AuditService $audit) {}

    public function placeFromCart(User $user, Cart $cart, array $data): Order
    {
        return DB::transaction(function () use ($user, $cart, $data): Order {
            $items = $cart->load('items')->items;

            if ($items->isEmpty()) {
                throw ValidationException::withMessages(['cart' => 'Your cart is empty.']);
            }

            $lines = [];
            $subtotalMinor = 0;

            foreach ($items as $item) {
                $product = Product::whereKey($item->product_id)
                    ->where('status', ProductStatus::ACTIVE)
                    ->lockForUpdate()
                    ->first();

                if (! $product) {
                    throw ValidationException::withMessages(['cart' => 'One or more products are no longer available.']);
                }

                $inventory = $product->inventory()->lockForUpdate()->first();

                if ($inventory?->track_inventory && $inventory->availableQuantity() < $item->quantity) {
                    throw ValidationException::withMessages(['cart' => 'Stock changed for '.$product->name.'. Please review your cart.']);
                }

                $unitPriceMinor = Money::toMinor((string) $product->price);
                $lineTotalMinor = $unitPriceMinor * $item->quantity;
                $subtotalMinor += $lineTotalMinor;

                $lines[] = [
                    'product' => $product,
                    'inventory' => $inventory,
                    'quantity' => $item->quantity,
                    'unit_price_minor' => $unitPriceMinor,
                    'line_total_minor' => $lineTotalMinor,
                ];
            }

            $shippingMinor = ShippingCalculator::calculate(Money::fromMinor($subtotalMinor));
            $discountMinor = 0;
            $totalMinor = $subtotalMinor + $shippingMinor - $discountMinor;
            $paymentMethod = $data['payment_method'];
            $shipping = $this->shippingData($user, $data);

            $order = Order::create([
                'order_number' => OrderNumberGenerator::generate(),
                'user_id' => $user->id,
                'status' => OrderStatus::PENDING,
                'payment_method' => $paymentMethod,
                'payment_status' => $paymentMethod === PaymentMethod::PAYSTACK
                    ? PaymentStatus::PENDING
                    : PaymentStatus::UNPAID,
                'payment_reference' => Str::upper($paymentMethod).'-'.Str::upper(Str::random(10)),
                'currency' => config('store.currency', 'NGN'),
                'subtotal' => Money::fromMinor($subtotalMinor),
                'shipping' => Money::fromMinor($shippingMinor),
                'discount' => Money::fromMinor($discountMinor),
                'total' => Money::fromMinor($totalMinor),
                'notes' => $data['notes'] ?? null,
                'placed_at' => now(),
            ] + $shipping);

            foreach ($lines as $line) {
                $order->items()->create([
                    'product_id' => $line['product']->id,
                    'name' => $line['product']->name,
                    'sku' => $line['product']->sku,
                    'quantity' => $line['quantity'],
                    'unit_price' => Money::fromMinor($line['unit_price_minor']),
                    'line_total' => Money::fromMinor($line['line_total_minor']),
                ]);

                if ($line['inventory']?->track_inventory) {
                    $line['inventory']->increment('reserved', $line['quantity']);
                }
            }

            $cart->items()->delete();

            $this->audit->record(
                'order.placed',
                $user,
                Order::class,
                $order->id,
                ['order_number' => $order->order_number, 'total' => $order->total],
            );

            return $order->load('items.product');
        });
    }

    public function cancel(Order $order, ?User $actor): void
    {
        if (in_array($order->status, [OrderStatus::CANCELLED, OrderStatus::REFUNDED, OrderStatus::DELIVERED], true)) {
            throw ValidationException::withMessages(['status' => 'This order can no longer be cancelled.']);
        }

        DB::transaction(function () use ($order, $actor): void {
            $order->update(['status' => OrderStatus::CANCELLED]);
            $this->releaseInventory($order);

            $this->audit->record(
                'order.cancelled',
                $actor,
                Order::class,
                $order->id,
                ['order_number' => $order->order_number],
            );
        });
    }

    public function updateStatus(Order $order, string $status, User $actor): void
    {
        if (! in_array($status, OrderStatus::ALL, true)) {
            throw ValidationException::withMessages(['status' => 'Unsupported order status.']);
        }

        if ($status !== $order->status && ! OrderStatus::canTransition($order->status, $status)) {
            throw ValidationException::withMessages(['status' => 'That status transition is not allowed.']);
        }

        DB::transaction(function () use ($order, $status, $actor): void {
            $previous = $order->status;
            $order->update(['status' => $status]);

            if (in_array($status, [OrderStatus::CANCELLED, OrderStatus::REFUNDED], true)) {
                $this->releaseInventory($order);
            }

            $this->audit->record(
                'order.status_updated',
                $actor,
                Order::class,
                $order->id,
                ['order_number' => $order->order_number, 'from' => $previous, 'to' => $status],
            );
        });
    }

    private function shippingData(User $user, array $data): array
    {
        if (! empty($data['address_id'])) {
            $address = Address::where('user_id', $user->id)->findOrFail($data['address_id']);

            return [
                'shipping_name' => $address->recipient_name,
                'shipping_phone' => $address->phone,
                'shipping_email' => $user->email,
                'shipping_line_1' => $address->line_1,
                'shipping_line_2' => $address->line_2,
                'shipping_city' => $address->city,
                'shipping_state' => $address->state,
                'shipping_postal_code' => $address->postal_code,
                'shipping_country' => $address->country,
            ];
        }

        return [
            'shipping_name' => $data['shipping_name'],
            'shipping_phone' => $data['shipping_phone'],
            'shipping_email' => $data['shipping_email'],
            'shipping_line_1' => $data['shipping_line_1'],
            'shipping_line_2' => $data['shipping_line_2'] ?? null,
            'shipping_city' => $data['shipping_city'],
            'shipping_state' => $data['shipping_state'] ?? null,
            'shipping_postal_code' => $data['shipping_postal_code'] ?? null,
            'shipping_country' => Str::upper($data['shipping_country'] ?? 'NG'),
        ];
    }

    private function releaseInventory(Order $order): void
    {
        if ($order->inventory_released_at !== null) {
            return;
        }

        foreach ($order->load('items')->items as $item) {
            if (! $item->product_id) {
                continue;
            }

            Inventory::where('product_id', $item->product_id)
                ->where('reserved', '>=', $item->quantity)
                ->decrement('reserved', $item->quantity);
        }

        $order->update(['inventory_released_at' => now()]);
    }
}
