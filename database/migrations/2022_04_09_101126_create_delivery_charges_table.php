<?php

use App\Enums\DeliveryType;
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


        Schema::create('delivery_charges', function (Blueprint $table) {
            $table->id();

            $table->unsignedTinyInteger('shipping_type')->comment(DeliveryType::SAMEDAY.'=Inside city,'.DeliveryType::NEXTDAY.'= Next day,'.DeliveryType::SUBCITY.'=Sub city, '.DeliveryType::OUTSIDECITY.'=Outside City');
            $table->unsignedTinyInteger('delivery_type_id')->nullable();
            //start from pincode details 
            $table->foreignId('from_country_id')->nullable();
            $table->foreignId('from_city_id')->nullable();
            $table->foreignId('from_district_id')->nullable();
            $table->foreignId('from_town_id')->nullable();
            $table->string('from_portal_code')->nullable();
            //end from pincode details
            //to pincode details
            $table->foreignId('to_country_id')->nullable();
            $table->foreignId('to_city_id')->nullable();
            $table->foreignId('to_district_id')->nullable();
            $table->foreignId('to_town_id')->nullable();
            $table->string('to_portal_code')->nullable();
            //end to pincode details
            
            //door 2 door
            $table->bigInteger('dtd_start_weight')->nullable();
            $table->bigInteger('dtd_end_weight')->nullable();
            $table->decimal('dtd_s_e_rate',22,2)->nullable();
            $table->decimal('dtd_additional_weight', 22, 2)->nullable();
            $table->decimal('dtd_additional_rate', 22, 2)->nullable();

            //door 2 hub
            $table->bigInteger('dth_start_weight');
            $table->bigInteger('dth_end_weight');
            $table->decimal('dth_s_e_rate',22,2);
            $table->decimal('dth_additional_weight', 22, 2)->nullable();
            $table->decimal('dth_additional_rate', 22, 2)->nullable();
            
            //hub 2 hub
            $table->bigInteger('hth_start_weight')->nullable();
            $table->bigInteger('hth_end_weight')->nullable();
            $table->decimal('hth_s_e_rate',22,2)->nullable();
            $table->decimal('hth_additional_weight', 22, 2)->nullable();
            $table->decimal('hth_additional_rate', 22, 2)->nullable();

            //hub 2 door
            $table->bigInteger('htd_start_weight');
            $table->bigInteger('htd_end_weight');
            $table->decimal('htd_s_e_rate',22,2);
            $table->decimal('htd_additional_weight', 22, 2)->nullable();
            $table->decimal('htd_additional_rate', 22, 2)->nullable();

            $table->integer('position')->nullable();
            $table->unsignedTinyInteger('status')->default(\App\Enums\Status::ACTIVE)->comment(\App\Enums\Status::ACTIVE.'='.trans('status.'.\App\Enums\Status::ACTIVE).', ' .\App\Enums\Status::INACTIVE.'='.trans('status.'.\App\Enums\Status::INACTIVE));
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
        Schema::dropIfExists('delivery_charges');
    }
};
