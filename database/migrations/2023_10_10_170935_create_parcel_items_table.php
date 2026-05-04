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
        Schema::create('parcel_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parcel_id')->constrained('parcels')->onUpdate('cascade')->onDelete('cascade');
            $table->integer('package_type_id')->nullable();
            $table->decimal('length', 16, 2)->nullable();
            $table->decimal('width', 16, 2)->nullable();
            $table->decimal('height', 16, 2)->nullable();
            $table->decimal('weight', 16, 2)->nullable();
            $table->decimal('quantity', 16, 2)->nullable();
            $table->integer('category_id')->nullable();
            $table->decimal('fragile_liquid_amount', 16, 2)->nullable();
            $table->integer('parcel_with_insurance')->nullable();
            $table->decimal('rush_hour_service', 16, 2)->nullable();
            $table->string('extra_cost')->nullable();
            $table->decimal('extra_cost_amount', 16, 2)->nullable();
            $table->text('extra_cost_description')->nullable();
            $table->integer('packaging_id')->nullable();
            $table->decimal('total_weight', 16, 2)->nullable();
            $table->decimal('total_cbm', 16, 3)->nullable();
            $table->decimal('unit_parcel_service_cost', 16, 3)->nullable();
            $table->text('description')->nullable();
            $table->text('content_parcel')->nullable();
            $table->decimal('parcel_value', 16, 2)->default(0)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parcel_items');
    }
};
