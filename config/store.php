<?php

return [
    'currency' => env('STORE_CURRENCY', 'NGN'),
    'shipping_flat_fee' => env('STORE_SHIPPING_FLAT_FEE', '2500'),
    'free_shipping_threshold' => env('STORE_FREE_SHIPPING_THRESHOLD', '150000'),
    'payment_methods' => [
        'whatsapp' => env('PAYMENT_WHATSAPP_ENABLED', true),
        'paystack' => env('PAYMENT_PAYSTACK_ENABLED', false),
    ],
];
