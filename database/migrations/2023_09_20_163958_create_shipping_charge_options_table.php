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
        Schema::create('shipping_charge_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipping_type_id')->constrained('shipping_types')->onUpdate('cascade')->onDelete('cascade');
            $table->decimal('from_km',16,2)->nullable();
            $table->decimal('to_km',16,2)->nullable();
            $table->decimal('basic_price',16,2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_charge_options');
    }
};
