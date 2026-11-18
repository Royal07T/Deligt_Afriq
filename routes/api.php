<?php

use App\Http\Controllers\Webhooks\PaystackController;
use Illuminate\Support\Facades\Route;

Route::post('/webhooks/paystack', PaystackController::class)->name('payments.paystack.webhook');
