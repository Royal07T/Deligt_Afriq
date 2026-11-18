<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateOrderStatusRequest;
use App\Models\Order;
use App\Notifications\OrderStatusChangedNotification;
use App\Services\Commerce\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(private readonly OrderService $orderService) {}

    public function index(): View
    {
        $this->authorize('viewAny', Order::class);

        return view('admin.orders.index', [
            'orders' => Order::query()
                ->with('user')
                ->when(request()->query('status'), fn ($query, $status) => $query->where('status', $status))
                ->when(request()->query('payment_status'), fn ($query, $status) => $query->where('payment_status', $status))
                ->latest()
                ->paginate(15)
                ->withQueryString(),
        ]);
    }

    public function show(Order $order): View
    {
        $this->authorize('view', $order);

        return view('admin.orders.show', [
            'order' => $order->load(['items.product', 'user']),
        ]);
    }

    public function updateStatus(UpdateOrderStatusRequest $request, Order $order): RedirectResponse
    {
        $this->authorize('update', $order);
        $this->orderService->updateStatus($order, $request->string('status')->toString(), $request->user());

        $order->user?->notify(new OrderStatusChangedNotification($order->refresh()));

        return redirect()->route('admin.orders.show', $order)->with('status', 'Order status updated.');
    }
}
