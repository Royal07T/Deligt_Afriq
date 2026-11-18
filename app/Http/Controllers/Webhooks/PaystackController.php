<?php

namespace App\Http\Controllers\Webhooks;

use App\Exceptions\PaystackException;
use App\Http\Controllers\Controller;
use App\Services\Payments\PaystackPaymentService;
use App\Support\Payments\PaystackClient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use JsonException;
use Throwable;

class PaystackController extends Controller
{
    public function __construct(
        private readonly PaystackClient $client,
        private readonly PaystackPaymentService $payments,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $payload = $request->getContent();

        if (! $this->client->validWebhookSignature(
            $payload,
            $request->header('Paystack-Signature'),
            $request->header('X-Paystack-Signature'),
        )) {
            return response()->json(['message' => 'Invalid signature.'], 401);
        }

        try {
            $event = json_decode($payload, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return response()->json(['message' => 'Invalid JSON payload.'], 400);
        }

        if (! is_array($event)) {
            return response()->json(['message' => 'Invalid JSON payload.'], 400);
        }

        try {
            $order = $this->payments->handleWebhook($event);
        } catch (PaystackException|ValidationException $exception) {
            Log::warning('Paystack webhook ignored: '.$exception->getMessage());

            return response()->json(['received' => true, 'ignored' => true]);
        } catch (Throwable $exception) {
            Log::error('Paystack webhook processing failed.', ['exception' => $exception]);

            return response()->json(['message' => 'Webhook processing failed.'], 500);
        }

        return response()->json(['received' => true, 'order_id' => $order?->id]);
    }
}
