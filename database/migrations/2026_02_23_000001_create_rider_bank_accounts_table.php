<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rider_bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rider_id')->unique()->constrained('users')->onUpdate('cascade')->onDelete('cascade');
            $table->string('bank_name')->nullable();
            $table->string('account_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('mobile_wallet_number')->nullable();
            $table->string('routing_number')->nullable();
            $table->timestamps();

            $table->index('rider_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rider_bank_accounts');
    }
};
