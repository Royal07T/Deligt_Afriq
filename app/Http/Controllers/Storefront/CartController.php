<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Http\Requests\Storefront\AddToCartRequest;
use App\Http\Requests\Storefront\UpdateCartItemRequest;
use App\Models\Product;
use App\Services\Commerce\CartService;
use App\Support\Commerce\Money;
use App\Support\Commerce\ShippingCalculator;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct(private readonly CartService $cartService) {}

    public function index(): View
    {
        $cart = $this->cartService->resolve();
        $subtotalMinor = $cart->subtotalMinor();
        $shippingMinor = $cart->isEmpty() ? 0 : ShippingCalculator::calculate(Money::fromMinor($subtotalMinor));

        return view('storefront.cart.index', [
            'cart' => $cart,
            'subtotalMinor' => $subtotalMinor,
            'shippingMinor' => $shippingMinor,
            'totalMinor' => $subtotalMinor + $shippingMinor,
        ]);
    }

    public function store(AddToCartRequest $request): RedirectResponse
    {
        $cart = $this->cartService->resolve();
        $product = Product::findOrFail($request->integer('product_id'));

        $this->cartService->add($cart, $product, $request->integer('quantity'));

        return redirect()->route('cart.index')->with('status', 'Added to your bag.');
    }

    public function update(UpdateCartItemRequest $request, Product $product): RedirectResponse
    {
        $cart = $this->cartService->resolve();
        $this->cartService->updateQuantity($cart, $product, $request->integer('quantity'));

        return redirect()->route('cart.index')->with('status', 'Your bag was updated.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $cart = $this->cartService->resolve();
        $this->cartService->remove($cart, $product);

        return redirect()->route('cart.index')->with('status', 'Item removed from your bag.');
    }
}
