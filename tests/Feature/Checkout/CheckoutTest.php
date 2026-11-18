<?php

namespace Tests\Feature\Checkout;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Notifications\OrderPlacedNotification;
use App\Support\Orders\PaymentMethod;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Notification::fake();
    }

    public function test_checkout_requires_authentication(): void
    {
        $this->get(route('checkout.index'))->assertRedirect(route('login'));
    }

    public function test_customer_can_place_order_and_stock_is_reserved(): void
    {
        $customer = $this->customer();
        $product = Product::factory()->create(['price' => '25000.00']);
        $product->inventory()->update(['quantity' => 5]);

        $this->actingAs($customer)
            ->post(route('cart.store'), ['product_id' => $product->id, 'quantity' => 2]);

        $this->actingAs($customer)
            ->post(route('checkout.store'), $this->checkoutPayload())
            ->assertSessionHasNoErrors();

        $order = Order::firstOrFail();

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'user_id' => $customer->id,
            'subtotal' => '50000.00',
            'shipping' => '2500.00',
            'total' => '52500.00',
            'payment_method' => PaymentMethod::WHATSAPP,
        ]);
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'unit_price' => '25000.00',
            'line_total' => '50000.00',
        ]);
        $this->assertSame(2, $product->inventory()->first()->reserved);
        $this->assertDatabaseCount('cart_items', 0);
        $this->assertDatabaseHas('activity_logs', [
            'action' => 'order.placed',
            'user_id' => $customer->id,
            'subject_id' => $order->id,
        ]);
        Notification::assertSentTo($customer, OrderPlacedNotification::class);
    }

    public function test_checkout_validates_shipping_details_and_terms(): void
    {
        $customer = $this->customer();
        $product = Product::factory()->create();

        $this->actingAs($customer)
            ->post(route('cart.store'), ['product_id' => $product->id, 'quantity' => 1]);

        $this->actingAs($customer)
            ->post(route('checkout.store'), [
                'payment_method' => PaymentMethod::WHATSAPP,
            ])
            ->assertSessionHasErrors([
                'shipping_name',
                'shipping_phone',
                'shipping_email',
                'shipping_line_1',
                'shipping_city',
                'shipping_country',
                'terms',
            ]);

        $this->assertDatabaseCount('orders', 0);
    }

    public function test_paystack_cannot_be_selected_before_it_is_enabled(): void
    {
        $customer = $this->customer();
        $product = Product::factory()->create();

        $this->actingAs($customer)
            ->post(route('cart.store'), ['product_id' => $product->id, 'quantity' => 1]);

        $this->actingAs($customer)
            ->post(route('checkout.store'), $this->checkoutPayload([
                'payment_method' => PaymentMethod::PAYSTACK,
            ]))
            ->assertSessionHasErrors('payment_method');

        $this->assertDatabaseCount('orders', 0);
    }

    public function test_whatsapp_handoff_page_is_available_to_order_owner(): void
    {
        $customer = $this->customer();
        $order = Order::factory()->create(['user_id' => $customer->id]);

        $this->actingAs($customer)
            ->get(route('checkout.payment', $order))
            ->assertOk()
            ->assertSee('Continue on WhatsApp');
    }

    private function customer(): User
    {
        return User::factory()->create();
    }

    private function checkoutPayload(array $overrides = []): array
    {
        return array_merge([
            'shipping_name' => 'Ada Okafor',
            'shipping_phone' => '+2348030613742',
            'shipping_email' => 'ada@example.test',
            'shipping_line_1' => '12 Silverbird Way',
            'shipping_city' => 'Abuja',
            'shipping_country' => 'ng',
            'payment_method' => PaymentMethod::WHATSAPP,
            'notes' => 'Call on arrival.',
            'terms' => '1',
        ], $overrides);
    }
}
