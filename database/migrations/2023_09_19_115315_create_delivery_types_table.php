<?php

use App\Enums\Status;
use App\Models\Backend\DeliveryType;
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
        Schema::create('delivery_types', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->unsignedTinyInteger('status')->default(Status::ACTIVE)->comment(Status::ACTIVE.'='.trans('status.'.Status::ACTIVE).', ' .Status::INACTIVE.'='.trans('status.'.Status::INACTIVE));
            $table->integer('position')->nullable(); 
            $table->timestamps();
        });


        DeliveryType::create([
            'id'       => 1,
            'title'    => 'Inside city',
            'position' => 1
        ]);
        DeliveryType::create([
            'id'       => 3,
            'title'    => 'Outside city',
            'position' => 3
        ]);
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_types');
    }
};
