<?php

use App\Models\Backend\District;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Seeder;
return new class extends Migration  
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('districts', function (Blueprint $table) {
            $table->id();
            $table->integer('city_id');
            $table->string('name');
            $table->timestamps();
        });

    }

    public function dummySeed(){
        $districts = [
            ['country_id' => 19, 'city_id'=>1,'name' => 'Feni']
        ]; 
        District::insert($districts);
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('districts');
    }




};
