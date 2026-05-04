<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parcel_disputes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parcel_id');
            $table->string('raised_by', 20);
            $table->string('reason_type', 50);
            $table->text('description')->nullable();
            $table->json('evidence_files')->nullable();
            $table->string('status', 20)->default('open');
            $table->text('admin_decision')->nullable();
            $table->string('liability', 20)->nullable();
            $table->decimal('refund_amount', 15, 2)->nullable();
            $table->decimal('rider_liability_amount', 15, 2)->nullable();
            $table->string('refund_method', 50)->nullable();
            $table->string('refund_reference_id', 100)->nullable();
            $table->unsignedBigInteger('refund_processed_by')->nullable();
            $table->timestamp('refund_processed_at')->nullable();
            $table->string('refund_status', 20)->default('pending');
            $table->text('refund_note')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->index(['parcel_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parcel_disputes');
    }
};
