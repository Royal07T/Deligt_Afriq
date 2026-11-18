<?php

namespace App\Http\Controllers\Storefront;

use App\Exceptions\PaystackException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Storefront\CheckoutRequest;
use App\Models\Order;
use App\Notifications\OrderPlacedNotification;
use App\Services\Commerce\CartService;
use App\Services\Commerce\OrderService;
use App\Services\Payments\PaystackPaymentService;
use App\Support\Commerce\Money;
use App\Support\Commerce\ShippingCalculator;
use App\Support\Orders\PaymentMethod;
use App\Support\Orders\PaymentStatus;
use App\Support\Orders\WhatsAppHandoff;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly CartService $cartService,
        private readonly OrderService $orderService,
        private readonly PaystackPaymentService $paystack,
    ) {}

    public function index(): View|RedirectResponse
    {
        $cart = $this->cartService->resolve();

        if ($cart->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Add an item to your bag before checking out.');
        }

        $subtotalMinor = $cart->subtotalMinor();
        $shippingMinor = ShippingCalculator::calculate(Money::fromMinor($subtotalMinor));

        return view('storefront.checkout.index', [
            'cart' => $cart,
            'addresses' => auth()->user()->addresses()->latest()->get(),
            'subtotalMinor' => $subtotalMinor,
            'shippingMinor' => $shippingMinor,
            'totalMinor' => $subtotalMinor + $shippingMinor,
            'paymentMethods' => PaymentMethod::available(),
        ]);
    }

    public function store(CheckoutRequest $request): RedirectResponse
    {
        $cart = $this->cartService->resolve();
        $order = $this->orderService->placeFromCart($request->user(), $cart, $request->validated());

        $request->user()->notify(new OrderPlacedNotification($order));

        if ($order->payment_method === PaymentMethod::PAYSTACK) {
            try {
                $payment = $this->paystack->initialize($order);
            } catch (PaystackException|ValidationException $exception) {
                return redirect()->route('checkout.payment', $order)->with('error', $exception->getMessage());
            }

            return redirect()->away($payment['authorization_url']);
        }

        return redirect()->route('checkout.payment', $order)->with('status', 'Your order has been placed.');
    }

    public function callback(Request $request): RedirectResponse
    {
        $reference = $request->query('reference');

        if (! is_string($reference) || $reference === '') {
            return redirect()->route('cart.index')->with('error', 'The payment reference was missing.');
        }

        $order = Order::where('payment_reference', $reference)->first();

        if (! $order) {
            return redirect()->route('cart.index')->with('error', 'We could not find that payment.');
        }

        $this->authorize('view', $order);

        try {
            $this->paystack->verifyAndConfirm($order, $reference);
        } catch (PaystackException|ValidationException $exception) {
            return redirect()->route('checkout.payment', $order)->with('error', $exception->getMessage());
        }

        return redirect()->route('checkout.payment', $order)
            ->with('status', 'Payment confirmed. Your order is now being prepared.');
    }

    public function retry(Order $order): RedirectResponse
    {
        $this->authorize('view', $order);

        try {
            $payment = $this->paystack->initialize($order);
        } catch (PaystackException|ValidationException $exception) {
            return redirect()->route('checkout.payment', $order)->with('error', $exception->getMessage());
        }

        return redirect()->away($payment['authorization_url']);
    }

    public function payment(Order $order): View
    {
        $this->authorize('view', $order);

        return view('storefront.checkout.payment', [
            'order' => $order->load('items'),
            'transactions' => $order->paymentTransactions()->latest()->get(),
            'isPaid' => $order->payment_status === PaymentStatus::PAID,
            'whatsappLink' => WhatsAppHandoff::link($order),
        ]);
    }
}
