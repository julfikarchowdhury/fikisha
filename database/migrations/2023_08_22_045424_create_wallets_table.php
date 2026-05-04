<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rider_wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rider_id')->unique()->constrained('users')->onUpdate('cascade')->onDelete('cascade');
            $table->decimal('balance', 15, 2)->default(0);
            $table->decimal('pending_withdraw_amount', 15, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('rider_wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rider_id')->constrained('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('parcel_id')->nullable()->constrained('parcels')->onUpdate('cascade')->onDelete('set null');
            $table->enum('type', ['credit', 'debit']);
            $table->decimal('amount', 15, 2);
            $table->string('description')->nullable();
            $table->timestamps();

            $table->index('rider_id');
            $table->index('parcel_id');
            $table->unique(['parcel_id', 'type']);
        });

        Schema::create('rider_withdraw_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rider_id')->constrained('users')->onUpdate('cascade')->onDelete('cascade');
            $table->decimal('amount', 15, 2);
            $table->enum('status', ['pending', 'approved', 'rejected', 'paid'])->default('pending');
            $table->timestamp('requested_at');
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users')->onUpdate('cascade')->onDelete('set null');
            $table->string('note')->nullable();
            $table->timestamps();

            $table->index('rider_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rider_withdraw_requests');
        Schema::dropIfExists('rider_wallet_transactions');
        Schema::dropIfExists('rider_wallets');
    }
};
