<?php

namespace Tests\Feature\Orders;

use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Notifications\OrderStatusChangedNotification;
use App\Support\Access\Roles;
use App\Support\Orders\OrderStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class OrderManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedAccessControl();
        Notification::fake();
    }

    public function test_customer_can_view_own_orders_but_not_others(): void
    {
        $customer = User::factory()->create();
        $own = Order::factory()->create(['user_id' => $customer->id]);
        $other = Order::factory()->create();

        $this->actingAs($customer)
            ->get(route('customer.orders.index'))
            ->assertOk()
            ->assertSee($own->order_number)
            ->assertDontSee($other->order_number);

        $this->actingAs($customer)
            ->get(route('customer.orders.show', $own))
            ->assertOk();

        $this->actingAs($customer)
            ->get(route('customer.orders.show', $other))
            ->assertForbidden();
    }

    public function test_customer_can_cancel_pending_order_and_stock_is_released(): void
    {
        $customer = User::factory()->create();
        $product = Product::factory()->create();
        $product->inventory()->update(['reserved' => 2]);

        $order = Order::factory()->create(['user_id' => $customer->id]);
        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $this->actingAs($customer)
            ->post(route('customer.orders.cancel', $order))
            ->assertRedirect(route('customer.orders.show', $order));

        $this->assertSame(OrderStatus::CANCELLED, $order->refresh()->status);
        $this->assertNotNull($order->inventory_released_at);
        $this->assertSame(0, $product->inventory()->first()->reserved);
    }

    public function test_staff_without_order_permission_cannot_view_admin_orders(): void
    {
        $staff = User::factory()->create();
        $staff->assignRole(Roles::CONTENT_MANAGER);

        $this->actingAs($staff)
            ->get(route('admin.orders.index'))
            ->assertForbidden();
    }

    public function test_admin_can_update_order_status_and_notify_customer(): void
    {
        $customer = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $customer->id]);

        $this->actingAs($this->superAdmin())
            ->put(route('admin.orders.update-status', $order), ['status' => OrderStatus::PROCESSING])
            ->assertRedirect(route('admin.orders.show', $order));

        $this->assertSame(OrderStatus::PROCESSING, $order->refresh()->status);
        Notification::assertSentTo($customer, OrderStatusChangedNotification::class);
    }

    public function test_addresses_are_owner_only(): void
    {
        $customer = User::factory()->create();
        $other = User::factory()->create();
        $address = Address::factory()->create(['user_id' => $customer->id]);

        $this->actingAs($customer)
            ->get(route('customer.addresses.index'))
            ->assertOk()
            ->assertSee($address->label);

        $this->actingAs($customer)
            ->get(route('customer.addresses.create'))
            ->assertOk();

        $this->actingAs($customer)
            ->get(route('customer.addresses.edit', $address))
            ->assertOk();

        $this->actingAs($other)
            ->get(route('customer.addresses.edit', $address))
            ->assertForbidden();
    }

    private function superAdmin(): User
    {
        $user = User::factory()->create();
        $user->assignRole(Roles::SUPER_ADMIN);

        return $user;
    }
}
