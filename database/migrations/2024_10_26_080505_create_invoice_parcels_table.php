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
        Schema::create('invoice_parcels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->onUpdate('cascade')->onDelete('cascade');
            $table->bigInteger('parcel_id')->nullable();
            $table->unsignedTinyInteger('parcel_status')->nullable();
            $table->decimal('total_delivery_amount',16,2)->nullable(); 
            $table->decimal('vat_amount',16,2)->nullable(); 
            $table->decimal('discount_amount',16,2)->nullable(); 
            $table->decimal('total_shipping_fee',16,2)->nullable(); 
            $table->decimal('current_payable',16,2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_parcels');
    }
};
