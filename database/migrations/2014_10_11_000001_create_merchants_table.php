<?php

use App\Enums\Status;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('merchants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onUpdate('cascade')->onDelete('cascade');
            $table->tinyInteger('account_type')->nullable();
            $table->string('business_name');
            $table->string('merchant_unique_id')->nullable();
            $table->decimal('current_balance', 16, 2)->default(0);
            $table->decimal('opening_balance', 16, 2)->default(0);
            $table->decimal('vat', 16, 2)->default(0);
            $table->longText('cod_charges')->nullable();
            $table->string('rc_number', 50)->nullable();
            $table->string('nif_number', 50)->nullable();
            $table->string('alternative_phone_number')->nullable();
            $table->string('sender_document')->nullable();
            $table->string('sender_document1')->nullable();
            $table->foreignId('nid_id')->nullable()->constrained('uploads')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('nid_back_id')->nullable()->constrained('uploads')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('trade_license')->nullable()->constrained('uploads')->onUpdate('cascade')->onDelete('cascade');
            $table->string('contract_document')->nullable();
            $table->date('payout_date')->nullable();
            $table->string('payment_period')->default(0)->comment('2 = 2days , after every 2days will auto payment invoice generate');
            $table->unsignedTinyInteger('status')->default(\App\Enums\Status::ACTIVE)->comment(\App\Enums\Status::ACTIVE . '=' . trans('status.' . \App\Enums\Status::ACTIVE) . ', ' . \App\Enums\Status::INACTIVE . '=' . trans('status.' . \App\Enums\Status::INACTIVE));
            $table->longText('address')->nullable();
            $table->decimal('return_charges', 16, 2)->default(100)->comment('100 = 100%  means full charge will received courier');
            $table->string('reference_name')->nullable();
            $table->string('reference_phone')->nullable();
            $table->decimal('discount',16,2)->default(0);
            $table->decimal('minimum_reaches_amount',16,2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('merchants');
    }
};
