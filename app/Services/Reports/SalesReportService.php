<?php

namespace App\Services\Reports;

use App\Models\Order;
use App\Models\OrderItem;
use App\Support\Commerce\Money;
use App\Support\Orders\OrderStatus;
use App\Support\Orders\PaymentStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SalesReportService
{
    public function summary(?string $from = null, ?string $to = null): array
    {
        $query = $this->orders($from, $to);
        $paid = (clone $query)->where('payment_status', PaymentStatus::PAID);
        $refunded = (clone $query)->where(function (Builder $query): void {
            $query->where('payment_status', PaymentStatus::REFUNDED)
                ->orWhere('status', OrderStatus::REFUNDED);
        });

        $orders = (int) (clone $query)->count();
        $paidOrders = (int) $paid->count();
        $revenueMinor = Money::toMinor((string) $paid->sum('total'));
        $refundsMinor = Money::toMinor((string) $refunded->sum('total'));
        $pendingOrders = (int) (clone $query)
            ->whereIn('payment_status', [PaymentStatus::UNPAID, PaymentStatus::PENDING])
            ->count();
        $cancelledOrders = (int) (clone $query)->where('status', OrderStatus::CANCELLED)->count();

        return [
            'orders' => $orders,
            'paid_orders' => $paidOrders,
            'pending_orders' => $pendingOrders,
            'cancelled_orders' => $cancelledOrders,
            'revenue_minor' => $revenueMinor,
            'refunds_minor' => $refundsMinor,
            'average_order_minor' => $paidOrders > 0 ? intdiv($revenueMinor, $paidOrders) : 0,
        ];
    }

    public function daily(?string $from = null, ?string $to = null): Collection
    {
        return $this->orders($from, $to)
            ->selectRaw('DATE(created_at) as order_date, COUNT(*) as orders_count, SUM(CASE WHEN payment_status = ? THEN total ELSE 0 END) as revenue_total', [PaymentStatus::PAID])
            ->groupBy('order_date')
            ->orderBy('order_date')
            ->get()
            ->map(fn (object $row): array => [
                'date' => (string) $row->order_date,
                'orders' => (int) $row->orders_count,
                'revenue_minor' => Money::toMinor((string) $row->revenue_total),
            ]);
    }

    public function topProducts(?string $from = null, ?string $to = null): Collection
    {
        return OrderItem::query()
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.payment_status', PaymentStatus::PAID)
            ->when($from, fn (Builder $query, string $value): Builder => $query
                ->where('orders.created_at', '>=', Carbon::parse($value)->startOfDay()))
            ->when($to, fn (Builder $query, string $value): Builder => $query
                ->where('orders.created_at', '<=', Carbon::parse($value)->endOfDay()))
            ->select(
                'order_items.product_id',
                'order_items.name',
                DB::raw('SUM(order_items.quantity) as units_sold'),
                DB::raw('SUM(order_items.line_total) as revenue_total'),
            )
            ->groupBy('order_items.product_id', 'order_items.name')
            ->orderByDesc('revenue_total')
            ->limit(5)
            ->get()
            ->map(fn (object $row): array => [
                'name' => (string) $row->name,
                'units' => (int) $row->units_sold,
                'revenue_minor' => Money::toMinor((string) $row->revenue_total),
            ]);
    }

    private function orders(?string $from, ?string $to): Builder
    {
        return Order::query()
            ->when($from, fn (Builder $query, string $value): Builder => $query
                ->where('created_at', '>=', Carbon::parse($value)->startOfDay()))
            ->when($to, fn (Builder $query, string $value): Builder => $query
                ->where('created_at', '<=', Carbon::parse($value)->endOfDay()));
    }
}
