<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('label', 60);
            $table->string('recipient_name');
            $table->string('phone', 30);
            $table->string('line_1');
            $table->string('line_2')->nullable();
            $table->string('city', 80);
            $table->string('state', 80)->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->char('country', 2)->default('NG');
            $table->boolean('is_default')->default(false);
            $table->timestamps();

            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
