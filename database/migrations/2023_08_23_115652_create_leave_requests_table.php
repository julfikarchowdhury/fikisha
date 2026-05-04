<?php

use App\Enums\LeaveStatus;
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
        Schema::create('leave_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); 
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
            $table->foreignId('leave_assign_id')->constrained('leave_assigns')->onDelete('cascade');
            $table->foreignId('type_id')->constrained('leave_types')->onDelete('cascade');
            $table->date('leave_from')->nullable();
            $table->date('leave_to')->nullable();
            $table->string('file')->nullable();
            $table->longText('reason')->nullable();
            $table->unsignedTinyInteger('status')->default(LeaveStatus::PENDING)->comment(LeaveStatus::PENDING.'= Pending, '.LeaveStatus::APPROVED.' = Approved, '.LeaveStatus::REJECTED.' = Rejected');
            $table->timestamps();

            $table->index('user_id'); 
            $table->index('role_id');
            $table->index('leave_assign_id');
            $table->index('leave_from');
            $table->index('leave_to');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_requests');
    }
};
