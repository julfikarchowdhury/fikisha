<?php

use App\Enums\DriverType;
use App\Enums\RiderStatus;
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
        Schema::create('delivery_man', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onUpdate('cascade')->onDelete('cascade');
            $table->string('vehicle_type')->nullable();
            $table->unsignedTinyInteger('status')->default(\App\Enums\Status::ACTIVE)->comment(\App\Enums\Status::ACTIVE.'='.trans('status.'.\App\Enums\Status::ACTIVE).', ' .\App\Enums\Status::INACTIVE.'='.trans('status.'.\App\Enums\Status::INACTIVE));
            $table->unsignedTinyInteger('rider_status')->default(RiderStatus::APPROVED);
            $table->unsignedTinyInteger('is_available')->default(1);
            $table->timestamp('kyc_submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->string('rejection_reason')->nullable();
            $table->decimal('delivery_charge',13, 2)->default(0);
            $table->decimal('pickup_charge',13, 2)->default(0);
            $table->decimal('return_charge',13, 2)->default(0);
            $table->decimal('current_balance',13, 2)->default(0);
            $table->decimal('opening_balance',13, 2)->default(0);
            $table->foreignId('driving_license_image_id')->nullable()->constrained('uploads')->onUpdate('cascade')->onDelete('cascade');
            $table->string('delivery_lat')->nullable();
            $table->string('delivery_long')->nullable();
            $table->integer('province_id')->nullable();
            $table->integer('city_id')->nullable();
            $table->string('driver_type')->default(DriverType::EMPLOYEE)->comment(DriverType::EMPLOYEE.' = Employee,'.DriverType::FREELANCER.' = Freelancer');
            $table->date('hiring_date')->nullable();
            $table->string('internal_id_no')->nullable();
            $table->string('residence_address')->nullable();
            $table->foreignId('front_side_scan')->nullable()->constrained('uploads')->onUpdate('cascade');
            $table->foreignId('back_side_scan')->nullable()->constrained('uploads')->onUpdate('cascade');
            $table->string('years_of_experience')->nullable();
            $table->string('social_security_no')->nullable();
            $table->year('year')->nullable();
            $table->string('registration_no')->nullable();
            $table->string('chassis_no')->nullable();
            $table->foreignId('regis_front_scan')->nullable()->constrained('uploads')->onUpdate('cascade');
            $table->foreignId('regis_back_scan')->nullable()->constrained('uploads')->onUpdate('cascade');
            $table->string('colour')->nullable();
            $table->foreignId('inspctn_check_scan')->nullable()->constrained('uploads')->onUpdate('cascade');
            $table->string('insurance_no')->nullable();
            $table->string('insurance_company')->nullable();
            $table->date('insurance_expiry_date')->nullable();
            $table->foreignId('insurance_crtfy_scan')->nullable()->constrained('uploads')->onUpdate('cascade');
            $table->string('tech_control_id')->nullable();
            $table->date('tech_c_expiry_date')->nullable();
            $table->foreignId('tech_c_scan')->nullable()->constrained('uploads')->onUpdate('cascade');
            $table->string('freelance_signed_contract')->nullable();
            $table->tinyInteger('driver_side_type')->default(1);
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
        Schema::dropIfExists('delivery_man');
    }
};
