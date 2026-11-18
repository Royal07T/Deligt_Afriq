<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\Commerce\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(private readonly OrderService $orderService) {}

    public function index(): View
    {
        return view('customer.orders.index', [
            'orders' => auth()->user()->orders()->with('items')->latest()->paginate(10),
        ]);
    }

    public function show(Order $order): View
    {
        $this->authorize('view', $order);

        return view('customer.orders.show', [
            'order' => $order->load('items.product'),
        ]);
    }

    public function cancel(Order $order): RedirectResponse
    {
        $this->authorize('cancel', $order);
        $this->orderService->cancel($order, auth()->user());

        return redirect()->route('customer.orders.show', $order)->with('status', 'Order cancelled.');
    }
}
