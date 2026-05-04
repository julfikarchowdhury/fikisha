<?php

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
        Schema::create('provinces', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('province_code')->nullable();
            $table->integer('position')->default(0);
            $table->longText('description')->nullable();
            $table->tinyInteger('status')->default(\App\Enums\Status::ACTIVE)->comment(\App\Enums\Status::ACTIVE.'='.trans('status.'.\App\Enums\Status::ACTIVE).', ' .\App\Enums\Status::INACTIVE.'='.trans('status.'.\App\Enums\Status::INACTIVE));
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('provinces');
    }
};
