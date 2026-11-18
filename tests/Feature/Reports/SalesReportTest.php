<?php

namespace Tests\Feature\Reports;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Support\Access\Roles;
use App\Support\Orders\OrderStatus;
use App\Support\Orders\PaymentStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SalesReportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedAccessControl();
    }

    public function test_admin_can_view_paid_revenue_and_top_products(): void
    {
        $customer = User::factory()->create();
        $product = Product::factory()->create(['name' => 'Delight Signature Gown']);

        $first = Order::factory()->create([
            'user_id' => $customer->id,
            'payment_status' => PaymentStatus::PAID,
            'status' => OrderStatus::PROCESSING,
            'total' => '100000.00',
            'paid_at' => now(),
        ]);
        OrderItem::factory()->create([
            'order_id' => $first->id,
            'product_id' => $product->id,
            'name' => $product->name,
            'quantity' => 2,
            'unit_price' => '50000.00',
            'line_total' => '100000.00',
        ]);

        $second = Order::factory()->create([
            'user_id' => $customer->id,
            'payment_status' => PaymentStatus::PAID,
            'status' => OrderStatus::PROCESSING,
            'total' => '50000.00',
            'paid_at' => now(),
        ]);
        OrderItem::factory()->create([
            'order_id' => $second->id,
            'product_id' => $product->id,
            'name' => $product->name,
            'quantity' => 1,
            'unit_price' => '50000.00',
            'line_total' => '50000.00',
        ]);

        Order::factory()->create([
            'user_id' => $customer->id,
            'payment_status' => PaymentStatus::PENDING,
            'status' => OrderStatus::AWAITING_PAYMENT,
            'total' => '70000.00',
        ]);
        Order::factory()->create([
            'user_id' => $customer->id,
            'payment_status' => PaymentStatus::REFUNDED,
            'status' => OrderStatus::REFUNDED,
            'total' => '30000.00',
        ]);

        $this->actingAs($this->superAdmin())
            ->get(route('admin.reports.index'))
            ->assertOk()
            ->assertSee('NGN 150,000.00')
            ->assertSee('Delight Signature Gown')
            ->assertSee('Paid orders');
    }

    public function test_report_filters_can_exclude_all_orders(): void
    {
        Order::factory()->create([
            'payment_status' => PaymentStatus::PAID,
            'status' => OrderStatus::PROCESSING,
            'paid_at' => now(),
        ]);

        $this->actingAs($this->superAdmin())
            ->get(route('admin.reports.index', ['from' => now()->addYear()->toDateString()]))
            ->assertOk()
            ->assertSee('NGN 0.00');
    }

    public function test_staff_without_reports_permission_cannot_view_reports(): void
    {
        $staff = User::factory()->create();
        $staff->assignRole(Roles::CONTENT_MANAGER);

        $this->actingAs($staff)
            ->get(route('admin.reports.index'))
            ->assertForbidden();
    }

    private function superAdmin(): User
    {
        $user = User::factory()->create();
        $user->assignRole(Roles::SUPER_ADMIN);

        return $user;
    }
}
