<?php

use App\Enums\BooleanStatus;
use App\Enums\PaymentMode;
use App\Enums\ExpressServicePaymentMethod;
use App\Enums\InvoiceStatus;
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
        Schema::create('parcels', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('merchant_id')->nullable();
            $table->bigInteger('delivery_man_id')->nullable();
            $table->string('sender_first_name')->nullable();
            $table->string('sender_last_name')->nullable();
            $table->string('sender_company_name')->nullable();
            $table->string('sender_email')->nullable();
            $table->string('sender_phone')->nullable();
            $table->bigInteger('merchant_shop_id')->nullable();
            $table->integer('from_sender')->default(Status::INACTIVE); // 0 = from sender , 1 = from hub
            $table->integer('from_state_id')->nullable();
            $table->string('from_town')->nullable();
            $table->string('from_building')->nullable();
            $table->integer('from_city_id')->nullable();
            $table->string('from_portal_code')->nullable();
            $table->integer('from_point_type')->default(1);
            $table->integer('to_point_type')->default(1);
            $table->integer('to_state_id')->nullable();
            $table->string('to_town')->nullable();
            $table->string('to_building')->nullable();
            $table->integer('to_city_id')->nullable();
            $table->string('to_portal_code')->nullable();
            $table->integer('from_account_type')->nullable();
            $table->integer('to_account_type')->nullable();
            $table->integer('customer_id')->nullable();
            $table->longText('sender_residential_address')->nullable();
            $table->longText('pickup_address')->nullable();
            $table->string('pickup_phone')->nullable();
            $table->bigInteger('to_merchant_id')->nullable();
            $table->string('customer_company_name')->nullable();
            $table->string('customer_first_name')->nullable();
            $table->string('customer_last_name')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('receiver_whatsapp_number')->nullable();
            $table->string('receiver_email')->nullable();
            $table->string('customer_phone')->nullable();
            $table->string('receiver_mpesa_phone')->nullable();
            $table->string('receiver_otp')->nullable();
            $table->timestamp('receiver_otp_sent_at')->nullable();
            $table->timestamp('receiver_otp_verified_at')->nullable();
            $table->unsignedBigInteger('delivery_proof_image_id')->nullable();
            $table->timestamp('delivery_proof_timestamp')->nullable();
            $table->decimal('delivery_proof_lat', 10, 7)->nullable();
            $table->decimal('delivery_proof_lng', 10, 7)->nullable();
            $table->longText('customer_address')->nullable();
            $table->string('invoice_no')->nullable();
            $table->unsignedTinyInteger('category_id')->nullable();
            $table->bigInteger('weight')->default(0)->nullable();
            $table->bigInteger('packaging_id')->nullable();
            $table->decimal('selling_price', 13, 2)->nullable();
            $table->decimal('parcel_value', 16, 2)->nullable();
            $table->string('parcel_file')->nullable();
            $table->decimal('liquid_fragile_amount', 13, 2)->nullable();
            $table->decimal('rush_hour_amount', 13, 2)->nullable();
            $table->decimal('scheduled_amount', 13, 2)->nullable();
            $table->decimal('total_extra_cost', 13, 2)->nullable();
            $table->decimal('packaging_amount', 13, 2)->nullable();
            $table->decimal('delivery_charge', 13, 2)->nullable();
            $table->decimal('commission_amount', 16, 2)->default(0);
            $table->decimal('rider_earning', 16, 2)->default(0);
            $table->decimal('base_delivery_charge', 16, 2)->default(0);
            $table->decimal('receiver_markup', 16, 2)->default(0);
            $table->decimal('final_paid_amount', 16, 2)->default(0);
            $table->decimal('commission_percent', 5, 2)->default(0);
            $table->decimal('platform_total_earning', 16, 2)->default(0);
            $table->bigInteger('vat')->nullable();
            $table->decimal('vat_amount', 13, 2)->nullable();
            $table->decimal('total_delivery_amount', 13, 2)->nullable();
            $table->decimal('current_payable', 13, 2)->nullable();
            $table->string('tracking_id')->nullable();
            $table->bigInteger('parcel_category_id')->nullable();
            $table->longText('note')->nullable();
            $table->unsignedTinyInteger('partial_delivered')->default(\App\Enums\BooleanStatus::NO)->comment('no=0,yes=1');
            $table->unsignedTinyInteger('status')->default(\App\Enums\ParcelStatus::MARKETPLACE_PENDING)->comment(
                'marketplace_pending                 = 71,
                 marketplace_accepted                = 72,
                 marketplace_picked_up               = 73,
                 marketplace_delivered               = 74,
                 marketplace_cancelled               = 75,
                 marketplace_sender_payment_awaiting = 76,
                 marketplace_receiver_payment_awaiting = 77'
            );
            $table->unsignedBigInteger('is_returned')->default(BooleanStatus::NO);// if this return this parcel , 1 = returned , 0 = not returned
            $table->tinyInteger('booking_status')->default(\App\Enums\Status::INACTIVE);
            $table->string('parcel_bank')->nullable();
            $table->unsignedTinyInteger('pick_type')->nullable();
            $table->dateTime('pickup_date')->nullable();
            $table->dateTime('delivery_date')->nullable();
            $table->decimal('return_charges', 13, 2)->nullable()->comment('received by merchant return charges');
            $table->dateTime('return_date_time')->nullable();
            $table->decimal('product_price', 13, 2)->default(0);
            $table->integer('who_pays_either')->nullable();
            $table->unsignedBigInteger('delivery_charge_id')->nullable();
            $table->decimal('distance_km', 16, 2)->nullable();
            $table->string('pickup_lat')->nullable();
            $table->string('pickup_long')->nullable();
            $table->longText('pickup_location')->nullable();
            $table->string('drop_latitude')->nullable();
            $table->string('drop_longitude')->nullable();
            $table->longText('drop_location')->nullable();
            $table->string('priority_type_id')->nullable();
            $table->string('from_whatsapp_number')->nullable();
            $table->string('to_whatsapp_number')->nullable();
            $table->longText('cbm_details')->nullable();
            $table->decimal('total_weight', 16, 2)->default(0);
            $table->decimal('total_cubic_meters', 16, 3)->default(0);
            $table->decimal('total_valumetric_weight', 16, 3)->default(0);
            $table->string('slots')->nullable();
            $table->decimal('special_discount', 16, 2)->nullable();
            $table->decimal('discount', 16, 2)->nullable()->comment('percentage %');
            $table->decimal('discount_amount', 16, 2)->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->uuid('tracking_token')->nullable()->unique();
            $table->unsignedBigInteger('is_packaged')->default(0);
            $table->integer('package_id')->nullable();
            $table->dateTime('departure_date_and_time')->nullable();
            $table->dateTime('arrival_date_and_time_to_province')->nullable();
            $table->tinyInteger('policy')->default(0);
            $table->tinyInteger('payment_status')->default(InvoiceStatus::UNPAID);
            $table->bigInteger('invoice_id')->nullable();
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
        Schema::dropIfExists('parcels');
    }
};
