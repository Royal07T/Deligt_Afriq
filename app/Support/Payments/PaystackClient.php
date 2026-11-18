<?php

namespace App\Support\Payments;

use App\Exceptions\PaystackException;
use App\Models\Order;
use Illuminate\Support\Facades\Http;

final class PaystackClient
{
    private const SIGNATURE_TOLERANCE_SECONDS = 300;

    public function initialize(Order $order, string $reference): array
    {
        $response = $this->request('post', '/transaction/initialize', [
            'email' => $order->shipping_email,
            'amount' => (string) $order->totalMinor(),
            'currency' => $order->currency,
            'reference' => $reference,
            'callback_url' => route('checkout.paystack.callback'),
            'metadata' => json_encode([
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'customer_name' => $order->shipping_name,
            ], JSON_THROW_ON_ERROR),
        ]);

        if (($response['status'] ?? false) !== true || ! is_array($response['data'] ?? null)) {
            throw new PaystackException('Paystack could not initialize this payment.');
        }

        if (empty($response['data']['authorization_url'])) {
            throw new PaystackException('Paystack did not return a checkout URL.');
        }

        return $response['data'];
    }

    public function verify(string $reference): array
    {
        $response = $this->request('get', '/transaction/verify/'.rawurlencode($reference));

        if (($response['status'] ?? false) !== true || ! is_array($response['data'] ?? null)) {
            throw new PaystackException('Paystack could not verify this payment.');
        }

        return $response['data'];
    }

    public function validWebhookSignature(string $payload, ?string $standardHeader, ?string $legacySignature): bool
    {
        if ($standardHeader && $this->verifyStandardSignature($payload, $standardHeader)) {
            return true;
        }

        if ($legacySignature && $this->verifyLegacySignature($payload, $legacySignature)) {
            return true;
        }

        return false;
    }

    private function verifyStandardSignature(string $payload, string $header): bool
    {
        $secret = (string) config('services.paystack.webhook_secret', '');

        if ($secret === '' || str_contains($secret, 'replace-with')) {
            return false;
        }

        $timestamp = null;
        $signatures = [];

        foreach (explode(',', $header) as $part) {
            [$key, $value] = array_pad(explode('=', trim($part), 2), 2, '');
            $key = strtolower(trim($key));
            $value = trim($value);

            if ($key === 't' && ctype_digit($value)) {
                $timestamp = $value;
            }

            if ($key === 'v1') {
                $parts = explode(':', $value, 2);

                if (count($parts) === 2) {
                    $signatures[] = $parts[1];
                }
            }
        }

        if ($timestamp === null || abs(time() - (int) $timestamp) > self::SIGNATURE_TOLERANCE_SECONDS) {
            return false;
        }

        $expected = hash_hmac('sha512', $timestamp.'.'.$payload, $secret);

        foreach ($signatures as $signature) {
            if (hash_equals($expected, $signature)) {
                return true;
            }
        }

        return false;
    }

    private function verifyLegacySignature(string $payload, string $signature): bool
    {
        $secret = (string) config('services.paystack.secret_key', '');

        if ($secret === '' || str_contains($secret, 'replace-with')) {
            return false;
        }

        return hash_equals(hash_hmac('sha512', $payload, $secret), $signature);
    }

    private function request(string $method, string $path, array $data = []): array
    {
        $url = rtrim((string) config('services.paystack.base_url', 'https://api.paystack.co'), '/').$path;

        $pendingRequest = Http::withToken((string) config('services.paystack.secret_key', ''))
            ->acceptJson()
            ->asJson()
            ->timeout(15)
            ->retry(2, 200);

        $response = $method === 'post' ? $pendingRequest->post($url, $data) : $pendingRequest->get($url);

        if ($response->failed()) {
            throw new PaystackException($response->json('message') ?: 'Paystack request failed.', $response->status());
        }

        $json = $response->json();

        if (! is_array($json)) {
            throw new PaystackException('Paystack returned an invalid response.');
        }

        return $json;
    }
}
