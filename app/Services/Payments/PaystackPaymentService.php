<?php

namespace App\Services\Payments;

use App\Exceptions\PaystackException;
use App\Models\Order;
use App\Models\PaymentTransaction;
use App\Notifications\PaymentReceivedNotification;
use App\Services\Access\AuditService;
use App\Support\Orders\OrderStatus;
use App\Support\Orders\PaymentMethod;
use App\Support\Orders\PaymentStatus;
use App\Support\Payments\PaymentTransactionStatus;
use App\Support\Payments\PaystackClient;
use App\Support\Payments\PaystackEvent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PaystackPaymentService
{
    public function __construct(
        private readonly PaystackClient $client,
        private readonly AuditService $audit,
    ) {}

    public function initialize(Order $order): array
    {
        if ($order->payment_method !== PaymentMethod::PAYSTACK) {
            throw ValidationException::withMessages(['payment' => 'This order is not payable with Paystack.']);
        }

        if ($order->payment_status === PaymentStatus::PAID) {
            throw ValidationException::withMessages(['payment' => 'This order has already been paid.']);
        }

        if (in_array($order->status, [OrderStatus::CANCELLED, OrderStatus::REFUNDED, OrderStatus::DELIVERED], true)) {
            throw ValidationException::withMessages(['payment' => 'This order can no longer be paid.']);
        }

        $reference = 'PS-'.strtoupper(Str::random(16));

        DB::transaction(function () use ($order, $reference): void {
            $locked = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();

            if ($locked->payment_status === PaymentStatus::PAID) {
                throw ValidationException::withMessages(['payment' => 'This order has already been paid.']);
            }

            $locked->update([
                'payment_reference' => $reference,
                'payment_status' => PaymentStatus::PENDING,
                'status' => OrderStatus::AWAITING_PAYMENT,
                'paid_at' => null,
            ]);

            PaymentTransaction::updateOrCreate([
                'reference' => $reference,
            ], [
                'order_id' => $locked->id,
                'provider' => 'paystack',
                'status' => PaymentTransactionStatus::PENDING,
                'amount_minor' => $locked->totalMinor(),
                'currency' => $locked->currency,
                'paid_at' => null,
            ]);

            $this->audit->record(
                'payment.initialized',
                $locked->user,
                Order::class,
                $locked->id,
                ['order_number' => $locked->order_number, 'reference' => $reference],
            );
        });

        try {
            $data = $this->client->initialize($order->fresh(), $reference);
        } catch (PaystackException $exception) {
            $this->markFailed($order->fresh(), $reference, [], $exception->getMessage());

            throw $exception;
        }

        if (! empty($data['reference']) && $data['reference'] !== $reference) {
            throw new PaystackException('Paystack returned an unexpected payment reference.');
        }

        PaymentTransaction::where('reference', $reference)->update(['payload' => $data]);

        return $data;
    }

    public function verifyAndConfirm(Order $order, string $reference): Order
    {
        if (! hash_equals((string) $order->payment_reference, $reference)) {
            throw ValidationException::withMessages(['payment' => 'This payment reference does not match the order.']);
        }

        if ($order->payment_status === PaymentStatus::PAID) {
            return $order->fresh();
        }

        $data = $this->client->verify($reference);

        if (($data['status'] ?? null) !== 'success') {
            $this->markFailed($order, $reference, $data, 'Paystack reported an unsuccessful payment.');

            throw ValidationException::withMessages(['payment' => 'Payment was not successful. Please try again.']);
        }

        $this->assertMatchesOrder($order, $data);

        return $this->confirm($order, $data);
    }

    public function handleWebhook(array $payload): ?Order
    {
        if (($payload['event'] ?? null) !== PaystackEvent::CHARGE_SUCCESS || ! is_array($payload['data'] ?? null)) {
            return null;
        }

        $data = $payload['data'];
        $reference = $data['reference'] ?? null;

        if (! is_string($reference) || $reference === '') {
            return null;
        }

        $order = Order::where('payment_reference', $reference)->first();

        if (! $order) {
            return null;
        }

        if (($data['status'] ?? null) !== 'success') {
            $this->markFailed($order, $reference, $data, 'Paystack reported an unsuccessful payment.');

            return $order->fresh();
        }

        $this->assertMatchesOrder($order, $data);

        return $this->confirm($order, $data);
    }

    private function confirm(Order $order, array $data): Order
    {
        return DB::transaction(function () use ($order, $data): Order {
            $locked = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();

            if ($locked->payment_status === PaymentStatus::PAID && $locked->paid_at !== null) {
                return $locked->fresh();
            }

            $cannotFulfil = in_array($locked->status, [OrderStatus::CANCELLED, OrderStatus::REFUNDED], true);
            $reference = (string) $locked->payment_reference;

            $locked->update([
                'payment_status' => PaymentStatus::PAID,
                'paid_at' => now(),
            ]);

            if (! $cannotFulfil && in_array($locked->status, [OrderStatus::PENDING, OrderStatus::AWAITING_PAYMENT], true)) {
                $locked->update(['status' => OrderStatus::PROCESSING]);
            }

            PaymentTransaction::updateOrCreate([
                'reference' => $reference,
            ], [
                'order_id' => $locked->id,
                'provider' => 'paystack',
                'status' => PaymentTransactionStatus::SUCCESS,
                'amount_minor' => $locked->totalMinor(),
                'currency' => $locked->currency,
                'external_id' => isset($data['id']) ? (string) $data['id'] : null,
                'payload' => $data,
                'paid_at' => now(),
            ]);

            $this->audit->record(
                $cannotFulfil ? 'payment.received_after_cancel' : 'payment.received',
                null,
                Order::class,
                $locked->id,
                ['order_number' => $locked->order_number, 'reference' => $reference],
            );

            $locked->user?->notify(new PaymentReceivedNotification($locked->fresh()));

            return $locked->fresh()->load('items');
        });
    }

    private function markFailed(Order $order, string $reference, array $data, string $message): void
    {
        if ($order->payment_status === PaymentStatus::PAID) {
            return;
        }

        $order->update(['payment_status' => PaymentStatus::FAILED]);

        PaymentTransaction::updateOrCreate([
            'reference' => $reference,
        ], [
            'order_id' => $order->id,
            'provider' => 'paystack',
            'status' => PaymentTransactionStatus::FAILED,
            'amount_minor' => $order->totalMinor(),
            'currency' => $order->currency,
            'payload' => [...$data, 'error' => $message],
            'paid_at' => null,
        ]);

        $this->audit->record(
            'payment.failed',
            null,
            Order::class,
            $order->id,
            ['order_number' => $order->order_number, 'reference' => $reference, 'message' => $message],
        );
    }

    private function assertMatchesOrder(Order $order, array $data): void
    {
        if (isset($data['reference']) && ! hash_equals((string) $order->payment_reference, (string) $data['reference'])) {
            throw new PaystackException('Payment reference does not match the order.');
        }

        if (isset($data['amount']) && (int) $data['amount'] !== $order->totalMinor()) {
            throw new PaystackException('Payment amount does not match the order total.');
        }

        if (isset($data['currency']) && strtoupper((string) $data['currency']) !== $order->currency) {
            throw new PaystackException('Payment currency does not match the order.');
        }
    }
}
