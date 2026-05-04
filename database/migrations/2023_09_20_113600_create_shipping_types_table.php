<?php

use App\Enums\Status;
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
        Schema::create('shipping_types', function (Blueprint $table) {
            $table->id(); 
            $table->foreignId('delivery_type_id')->constrained('delivery_types')->onUpdate('cascade')->onDelete('cascade');
            $table->string('title')->nullable();
            $table->decimal('basic_price',16,2)->nullable(); 
            $table->decimal('start_weight',16,2)->nullable(); 
            $table->decimal('end_weight',16,2)->nullable(); 
            $table->decimal('addi_weight_price',16,2)->nullable(); //per weight price
            $table->decimal('start_volume',16,3)->nullable(); 
            $table->decimal('end_volume',16,3)->nullable(); 
            $table->decimal('addi_volume_price',16,2)->nullable(); //per volume price
            $table->string('slots')->nullable(); //slots
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_types');
    }
};
