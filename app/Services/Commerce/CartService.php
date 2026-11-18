<?php

namespace App\Services\Commerce;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Support\Catalogue\ProductStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CartService
{
    public function __construct(private readonly Request $request) {}

    public function resolve(): Cart
    {
        $user = $this->request->user();
        $token = $this->request->session()->get('cart.token');

        if ($user) {
            $cart = Cart::firstOrCreate(['user_id' => $user->id]);

            if ($token) {
                $guestCart = Cart::whereNull('user_id')->where('session_id', $token)->first();

                if ($guestCart) {
                    $this->merge($cart, $guestCart);
                    $guestCart->delete();
                }

                $this->request->session()->forget('cart.token');
            }

            return $cart->load('items');
        }

        if (! $token) {
            $token = Str::random(40);
            $this->request->session()->put('cart.token', $token);
        }

        return Cart::firstOrCreate(['session_id' => $token])->load('items');
    }

    public function add(Cart $cart, Product $product, int $quantity): Cart
    {
        $quantity = max(1, $quantity);
        $product = Product::whereKey($product->id)->where('status', ProductStatus::ACTIVE)->first();

        if (! $product) {
            throw ValidationException::withMessages(['product' => 'This product is no longer available.']);
        }

        $available = $this->availableQuantity($product);
        $existing = $cart->items()->where('product_id', $product->id)->first();
        $requested = $quantity + ($existing?->quantity ?? 0);

        if ($requested > $available) {
            throw ValidationException::withMessages(['quantity' => 'Only '.$available.' unit(s) are available.']);
        }

        CartItem::updateOrCreate(
            ['cart_id' => $cart->id, 'product_id' => $product->id],
            ['quantity' => $requested],
        );

        return $cart->load('items');
    }

    public function updateQuantity(Cart $cart, Product $product, int $quantity): Cart
    {
        if ($quantity < 1) {
            return $this->remove($cart, $product);
        }

        $available = $this->availableQuantity($product);

        if ($quantity > $available) {
            throw ValidationException::withMessages(['quantity' => 'Only '.$available.' unit(s) are available.']);
        }

        $cart->items()->where('product_id', $product->id)->update(['quantity' => $quantity]);

        return $cart->load('items');
    }

    public function remove(Cart $cart, Product $product): Cart
    {
        $cart->items()->where('product_id', $product->id)->delete();

        return $cart->load('items');
    }

    public function clear(Cart $cart): void
    {
        $cart->items()->delete();
    }

    public function itemCount(Cart $cart): int
    {
        return $cart->load('items')->itemCount();
    }

    private function merge(Cart $destination, Cart $source): void
    {
        foreach ($source->load('items')->items as $item) {
            $product = $item->product;

            if (! $product || $product->status !== ProductStatus::ACTIVE) {
                continue;
            }

            $available = $this->availableQuantity($product);

            if ($available < 1) {
                continue;
            }

            $existing = $destination->items()->where('product_id', $product->id)->first();
            $quantity = min($available, ($existing?->quantity ?? 0) + $item->quantity);

            if ($existing) {
                $existing->update(['quantity' => $quantity]);
            } else {
                $destination->items()->create(['product_id' => $product->id, 'quantity' => $quantity]);
            }
        }
    }

    private function availableQuantity(Product $product): int
    {
        $inventory = $product->inventory;

        if (! $inventory || ! $inventory->track_inventory) {
            return PHP_INT_MAX;
        }

        return $inventory->availableQuantity();
    }
}
