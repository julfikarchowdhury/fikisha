<?php

use App\Enums\AttendanceStatus;
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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); 
            $table->date('date')->nullable();
            $table->string('in_ip_address')->nullable();
            $table->string('out_ip_address')->nullable();
            $table->string('check_in')->nullable();
            $table->string('check_out')->nullable();
            $table->string('stay_time')->nullable()->comment('Minutes');
            $table->string('over_stay_time')->nullable()->comment('Minutes');
            $table->unsignedTinyInteger('status')->default(AttendanceStatus::CHECK_IN)->comment(AttendanceStatus::CHECK_IN.' = Check in,'.AttendanceStatus::CHECK_OUT.' = Check out');
            $table->timestamps();

            $table->index('user_id');  
            $table->index('date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
