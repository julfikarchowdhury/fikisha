<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mpesa_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_id')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedBigInteger('parcel_id')->nullable();
            $table->string('checkout_request_id')->nullable()->index();
            $table->string('merchant_request_id')->nullable()->index();
            $table->string('phone')->nullable();
            $table->decimal('amount', 10, 2)->default(0);
            $table->string('status')->default('pending'); // pending, success, failed
            $table->json('parcel_payload')->nullable();
            $table->json('mpesa_response')->nullable();
            $table->json('callback_payload')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mpesa_payments');
    }
};
