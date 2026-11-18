<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status', 30)->default('pending')->index();
            $table->string('payment_method', 30);
            $table->string('payment_status', 30)->default('unpaid')->index();
            $table->string('payment_reference')->nullable()->unique();
            $table->char('currency', 3)->default('NGN');
            $table->decimal('subtotal', 14, 2);
            $table->decimal('shipping', 14, 2)->default(0);
            $table->decimal('discount', 14, 2)->default(0);
            $table->decimal('total', 14, 2);
            $table->string('shipping_name');
            $table->string('shipping_phone', 30);
            $table->string('shipping_email');
            $table->string('shipping_line_1');
            $table->string('shipping_line_2')->nullable();
            $table->string('shipping_city', 80);
            $table->string('shipping_state', 80)->nullable();
            $table->string('shipping_postal_code', 20)->nullable();
            $table->char('shipping_country', 2)->default('NG');
            $table->text('notes')->nullable();
            $table->timestamp('placed_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('inventory_released_at')->nullable();
            $table->timestamps();

            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
