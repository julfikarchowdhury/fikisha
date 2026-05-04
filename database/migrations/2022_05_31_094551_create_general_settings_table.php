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
        Schema::create('general_settings', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->longText('address')->nullable();
            $table->string('currency')->nullable();
            $table->string('about')->nullable();
            $table->string('copyright')->nullable();
            $table->integer('logo')->nullable();
            $table->integer('light_logo')->nullable();
            $table->integer('favicon')->nullable();
            $table->integer('mobile_app_logo')->nullable();
            $table->string('current_version')->nullable();
            $table->string('par_track_prefix')->nullable();
            $table->string('invoice_prefix')->nullable();
            $table->string('order_invoice_prefix')->nullable();
            $table->string('primary_color')->default('#7e0095')->nullable();
            $table->string('text_color')->default('#ffffff')->nullable();
            $table->string('location_system')->default(2)->nullable();
            $table->unsignedInteger('max_active_parcels_per_rider')->default(1);
            $table->decimal('rider_min_withdrawal_amount', 15, 2)->default(500);
            $table->decimal('marketplace_commission_percent', 5, 2)->default(0);
            $table->decimal('marketplace_base_fare', 16, 2)->default(0);
            $table->decimal('marketplace_per_km_rate', 16, 2)->default(0);
            $table->decimal('marketplace_per_kg_rate', 16, 2)->default(0);
            $table->decimal('marketplace_receiver_markup_percent', 5, 2)->default(0);
            $table->string('marketplace_pricing_mode')->default('distance');
            $table->decimal('inside_city_base_fare', 16, 2)->default(0);
            $table->decimal('inside_city_per_km_rate', 16, 2)->default(0);
            $table->decimal('inside_city_per_kg_rate', 16, 2)->default(0);
            $table->decimal('outside_city_base_fare', 16, 2)->default(0);
            $table->decimal('outside_city_per_km_rate', 16, 2)->default(0);
            $table->decimal('outside_city_per_kg_rate', 16, 2)->default(0);
            $table->decimal('inside_city_distance',16,2)->default(0);
            $table->string('country_code')->nullable();
            $table->bigInteger('default_country')->nullable();
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
        Schema::dropIfExists('general_settings');
    }
};
