<?php

use App\Models\Backend\Town;
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
        Schema::create('towns', function (Blueprint $table) {
            $table->id();
            $table->integer('city_id');
            $table->integer('district_id');
            $table->string('name')->nullable();
            $table->string('portal_code')->nullable();
            $table->timestamps();
        });
    
    }

    public function dummySeed(){
        $districts = [
            ['country_id' => 19, 'city_id'=>1,'district_id'=>1,'name' => 'Chhagalnaia','portal_code' => 3910],
            ['country_id' => 19, 'city_id'=>1,'district_id'=>1,'name' => 'Daraga Hat','portal_code'  => 3912]
        ]; 
        Town::insert($districts);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('towns');
    }
};
