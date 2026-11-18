<?php

namespace Tests\Feature\Payments;

use App\Models\ActivityLog;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Notifications\OrderPlacedNotification;
use App\Notifications\PaymentReceivedNotification;
use App\Support\Orders\OrderStatus;
use App\Support\Orders\PaymentMethod;
use App\Support\Orders\PaymentStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request as ClientRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PaystackPaymentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('services.paystack.base_url', 'https://api.paystack.co');
        config()->set('services.paystack.secret_key', 'sk_test_delight_secret');
        config()->set('services.paystack.webhook_secret', 'whsec_delight_secret');
        config()->set('store.payment_methods.paystack', true);

        Http::preventStrayRequests();
        Notification::fake();
    }

    public function test_paystack_checkout_initializes_payment_and_redirects_to_paystack(): void
    {
        Http::fake(function (ClientRequest $request) {
            return Http::response([
                'status' => true,
                'message' => 'Authorization URL created',
                'data' => [
                    'authorization_url' => 'https://checkout.paystack.test/payment',
                    'access_code' => 'ACCESS123',
                    'reference' => $request->data()['reference'],
                ],
            ]);
        });

        $customer = User::factory()->create(['email' => 'buyer@example.test']);
        $this->addProductToCart($customer);

        $this->actingAs($customer)
            ->post(route('checkout.store'), $this->checkoutPayload())
            ->assertStatus(302)
            ->assertHeader('Location', 'https://checkout.paystack.test/payment');

        Http::assertSent(function (ClientRequest $request): bool {
            return str_contains($request->url(), '/transaction/initialize')
                && $request->data()['amount'] === '5250000'
                && $request->data()['email'] === 'buyer@example.test';
        });

        $order = Order::firstOrFail();

        $this->assertSame(PaymentStatus::PENDING, $order->payment_status);
        $this->assertSame(OrderStatus::AWAITING_PAYMENT, $order->status);
        $this->assertDatabaseHas('payment_transactions', [
            'order_id' => $order->id,
            'status' => 'pending',
            'amount_minor' => 5250000,
        ]);
        $this->assertDatabaseHas('activity_logs', [
            'action' => 'payment.initialized',
            'subject_id' => $order->id,
        ]);
        Notification::assertSentTo($customer, OrderPlacedNotification::class);
    }

    public function test_callback_verifies_payment_and_marks_order_processing(): void
    {
        $customer = User::factory()->create();
        $order = $this->paystackOrder($customer);

        Http::fake([
            'api.paystack.co/transaction/verify/*' => Http::response([
                'status' => true,
                'message' => 'Verification successful',
                'data' => [
                    'id' => 987654321,
                    'status' => 'success',
                    'reference' => $order->payment_reference,
                    'amount' => 5250000,
                    'currency' => 'NGN',
                ],
            ]),
        ]);

        $this->actingAs($customer)
            ->get(route('checkout.paystack.callback', ['reference' => $order->payment_reference]))
            ->assertRedirect(route('checkout.payment', $order))
            ->assertSessionHas('status');

        $this->assertSame(PaymentStatus::PAID, $order->refresh()->payment_status);
        $this->assertSame(OrderStatus::PROCESSING, $order->status);
        $this->assertNotNull($order->paid_at);
        $this->assertDatabaseHas('payment_transactions', [
            'order_id' => $order->id,
            'status' => 'success',
            'external_id' => '987654321',
        ]);
        $this->assertDatabaseHas('activity_logs', [
            'action' => 'payment.received',
            'subject_id' => $order->id,
        ]);
        Notification::assertSentTo($customer, PaymentReceivedNotification::class);
    }

    public function test_callback_with_unsuccessful_payment_keeps_order_unpaid(): void
    {
        $customer = User::factory()->create();
        $order = $this->paystackOrder($customer);

        Http::fake([
            'api.paystack.co/transaction/verify/*' => Http::response([
                'status' => true,
                'data' => [
                    'status' => 'abandoned',
                    'reference' => $order->payment_reference,
                    'amount' => 5250000,
                    'currency' => 'NGN',
                ],
            ]),
        ]);

        $this->actingAs($customer)
            ->get(route('checkout.paystack.callback', ['reference' => $order->payment_reference]))
            ->assertRedirect(route('checkout.payment', $order))
            ->assertSessionHas('error');

        $this->assertSame(PaymentStatus::FAILED, $order->refresh()->payment_status);
        $this->assertSame(OrderStatus::AWAITING_PAYMENT, $order->status);
        Notification::assertNothingSent();
    }

    public function test_webhook_with_legacy_signature_marks_order_paid_once(): void
    {
        $customer = User::factory()->create();
        $order = $this->paystackOrder($customer);
        $payload = $this->chargeSuccessPayload($order);
        $raw = json_encode($payload, JSON_THROW_ON_ERROR);
        $signature = hash_hmac('sha512', $raw, 'sk_test_delight_secret');

        $this->postWebhook($raw, ['HTTP_X_PAYSTACK_SIGNATURE' => $signature])->assertOk();
        $this->postWebhook($raw, ['HTTP_X_PAYSTACK_SIGNATURE' => $signature])->assertOk();

        $this->assertSame(PaymentStatus::PAID, $order->refresh()->payment_status);
        $this->assertSame(OrderStatus::PROCESSING, $order->status);
        $this->assertSame(1, ActivityLog::where('action', 'payment.received')->where('subject_id', $order->id)->count());
        Notification::assertSentToTimes($customer, PaymentReceivedNotification::class, 1);
    }

    public function test_webhook_with_timestamped_signature_marks_order_paid(): void
    {
        $customer = User::factory()->create();
        $order = $this->paystackOrder($customer);
        $raw = json_encode($this->chargeSuccessPayload($order), JSON_THROW_ON_ERROR);
        $timestamp = (string) time();
        $signature = hash_hmac('sha512', $timestamp.'.'.$raw, 'whsec_delight_secret');

        $this->postWebhook($raw, [
            'HTTP_PAYSTACK_SIGNATURE' => "t={$timestamp},v1=whk_test:{$signature}",
        ])->assertOk();

        $this->assertSame(PaymentStatus::PAID, $order->refresh()->payment_status);
        $this->assertSame(OrderStatus::PROCESSING, $order->status);
    }

    public function test_webhook_rejects_invalid_and_stale_signatures(): void
    {
        $order = $this->paystackOrder(User::factory()->create());
        $raw = json_encode($this->chargeSuccessPayload($order), JSON_THROW_ON_ERROR);

        $this->postWebhook($raw, ['HTTP_X_PAYSTACK_SIGNATURE' => str_repeat('a', 128)])
            ->assertUnauthorized();

        $stale = (string) (time() - 601);
        $signature = hash_hmac('sha512', $stale.'.'.$raw, 'whsec_delight_secret');

        $this->postWebhook($raw, [
            'HTTP_PAYSTACK_SIGNATURE' => "t={$stale},v1=whk_test:{$signature}",
        ])->assertUnauthorized();

        $this->assertSame(PaymentStatus::PENDING, $order->refresh()->payment_status);
    }

    public function test_webhook_with_wrong_amount_does_not_mark_order_paid(): void
    {
        $order = $this->paystackOrder(User::factory()->create());
        $payload = $this->chargeSuccessPayload($order);
        $payload['data']['amount'] = 100;
        $raw = json_encode($payload, JSON_THROW_ON_ERROR);
        $signature = hash_hmac('sha512', $raw, 'sk_test_delight_secret');

        $this->postWebhook($raw, ['HTTP_X_PAYSTACK_SIGNATURE' => $signature])
            ->assertOk()
            ->assertJson(['ignored' => true]);

        $this->assertSame(PaymentStatus::PENDING, $order->refresh()->payment_status);
    }

    public function test_retry_uses_a_fresh_payment_reference(): void
    {
        $customer = User::factory()->create();
        $order = $this->paystackOrder($customer);
        $originalReference = $order->payment_reference;

        Http::fake(function (ClientRequest $request) {
            return Http::response([
                'status' => true,
                'data' => [
                    'authorization_url' => 'https://checkout.paystack.test/retry',
                    'access_code' => 'ACCESS456',
                    'reference' => $request->data()['reference'],
                ],
            ]);
        });

        $this->actingAs($customer)
            ->post(route('checkout.paystack.retry', $order))
            ->assertStatus(302)
            ->assertHeader('Location', 'https://checkout.paystack.test/retry');

        $this->assertNotSame($originalReference, $order->refresh()->payment_reference);
    }

    private function addProductToCart(User $customer): Product
    {
        $product = Product::factory()->create(['price' => '25000.00']);
        $product->inventory()->update(['quantity' => 10]);

        $this->actingAs($customer)
            ->post(route('cart.store'), ['product_id' => $product->id, 'quantity' => 2]);

        return $product;
    }

    private function paystackOrder(User $customer): Order
    {
        return Order::factory()->create([
            'user_id' => $customer->id,
            'payment_method' => PaymentMethod::PAYSTACK,
            'payment_status' => PaymentStatus::PENDING,
            'status' => OrderStatus::AWAITING_PAYMENT,
            'payment_reference' => 'PS-TESTREFERENCE01',
        ]);
    }

    private function chargeSuccessPayload(Order $order): array
    {
        return [
            'event' => 'charge.success',
            'data' => [
                'id' => 123456789,
                'status' => 'success',
                'reference' => $order->payment_reference,
                'amount' => 5250000,
                'currency' => 'NGN',
            ],
        ];
    }

    private function postWebhook(string $raw, array $headers = [])
    {
        return $this->call(
            'POST',
            '/api/webhooks/paystack',
            [],
            [],
            [],
            array_merge(['CONTENT_TYPE' => 'application/json'], $headers),
            $raw,
        );
    }

    private function checkoutPayload(): array
    {
        return [
            'shipping_name' => 'Ada Okafor',
            'shipping_phone' => '+2348030613742',
            'shipping_email' => 'buyer@example.test',
            'shipping_line_1' => '12 Silverbird Way',
            'shipping_city' => 'Abuja',
            'shipping_country' => 'NG',
            'payment_method' => PaymentMethod::PAYSTACK,
            'terms' => '1',
        ];
    }
}
