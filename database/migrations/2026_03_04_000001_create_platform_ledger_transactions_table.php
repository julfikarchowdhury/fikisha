<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('platform_ledger_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parcel_id')->nullable();
            $table->string('type', 50);
            $table->string('direction', 10);
            $table->decimal('amount', 16, 2);
            $table->string('reference_id', 100)->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['parcel_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('platform_ledger_transactions');
    }
};
