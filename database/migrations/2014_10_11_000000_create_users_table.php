<?php

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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('email_verify_token', 100)->nullable();
            $table->string('password')->nullable();
            $table->string('country_code', 10)->nullable();
            $table->string('mobile')->nullable();
            $table->string('nid_number')->nullable();
            $table->foreignId('designation_id')->nullable()->constrained('designations')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('department_id')->nullable()->constrained('departments')->onDelete('cascade');
            $table->unsignedTinyInteger('user_type')->default(\App\Enums\UserType::ADMIN)->comment(\App\Enums\UserType::ADMIN . '=' . trans('userType.' . \App\Enums\UserType::ADMIN) . ', ' . \App\Enums\UserType::MERCHANT . '=' . trans('userType.' . \App\Enums\UserType::MERCHANT) . ', ' . \App\Enums\UserType::DELIVERYMAN . '=' . trans('userType.' . \App\Enums\UserType::DELIVERYMAN) . ', ' . \App\Enums\UserType::INCHARGE . '=' . trans('userType.' . \App\Enums\UserType::INCHARGE))->nullable();
            $table->foreignId('image_id')->nullable()->constrained('uploads')->onUpdate('cascade')->onDelete('cascade');
            $table->string('joining_date')->nullable();
            $table->string('address')->nullable();
            $table->foreignId('role_id')->nullable()->constrained('roles')->onDelete('cascade');
            $table->text('permissions')->nullable();
            $table->integer('otp')->nullable();
            $table->string('password_reset_otp', 10)->nullable();
            $table->timestamp('password_reset_otp_expires_at')->nullable();
            $table->timestamp('password_reset_verified_at')->nullable();
            $table->string('password_reset_token', 100)->nullable();
            $table->decimal('salary', 16, 2)->default(0);
            $table->decimal('balance', 16, 2)->default(0);
            $table->decimal('per_hour_salary', 16, 2)->default(0);
            $table->string('device_token')->nullable();
            $table->string('web_token')->nullable();
            $table->unsignedTinyInteger('status')->default(\App\Enums\Status::ACTIVE)->comment(\App\Enums\Status::ACTIVE . '=' . trans('status.' . \App\Enums\Status::ACTIVE) . ', ' . \App\Enums\Status::INACTIVE . '=' . trans('status.' . \App\Enums\Status::INACTIVE));
            $table->unsignedTinyInteger('verification_status')->default(\App\Enums\Status::ACTIVE)->comment(\App\Enums\Status::ACTIVE . '=' . trans('status.' . \App\Enums\Status::ACTIVE) . ', ' . \App\Enums\Status::INACTIVE . '=' . trans('status.' . \App\Enums\Status::INACTIVE));
            $table->unsignedTinyInteger('document_status')->default(\App\Enums\Status::ACTIVE)->comment(\App\Enums\Status::ACTIVE . '=' . trans('status.' . \App\Enums\Status::ACTIVE) . ', ' . \App\Enums\Status::INACTIVE . '=' . trans('status.' . \App\Enums\Status::INACTIVE));
            $table->unsignedTinyInteger('submit_status')->default(\App\Enums\Status::ACTIVE)->comment(\App\Enums\Status::ACTIVE . '=' . trans('status.' . \App\Enums\Status::ACTIVE) . ', ' . \App\Enums\Status::INACTIVE . '=' . trans('status.' . \App\Enums\Status::INACTIVE));
            $table->string('google_id')->unique()->nullable();
            $table->string('facebook_id')->unique()->nullable();
            $table->timestamp('mobile_verified_at')->nullable();
            $table->bigInteger('province_id')->nullable();
            $table->bigInteger('city_id')->nullable();
            $table->bigInteger('district_id')->nullable();
            $table->bigInteger('town_id')->nullable();
            $table->string('portal_code')->nullable();
            $table->string('location')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();

            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
};
