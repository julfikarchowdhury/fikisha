<?php

namespace App\Models\Backend\HRM;

use App\Enums\AttendanceStatus;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

      
    public function user(){
        return $this->belongsTo(User::class,'user_id','id');
    }

    public function getStaytimeAttribute(){
        if($this->status == AttendanceStatus::CHECK_OUT):
            $totalHours        = Carbon::parse($this->check_in)->diffInHours($this->check_out);
            $totalHoursMinutes = $totalHours * 60; 
            $totalMinutes      = Carbon::parse($this->check_in)->diffInMinutes($this->check_out);
            $minutes           = ($totalMinutes - $totalHoursMinutes);
            return $totalHours.' H '.$minutes.' Minutes';
        endif;
        return __('parcel.not_check_out');
    }
    public function getOvertimeAttribute(){
        if($this->status == AttendanceStatus::CHECK_OUT): 
            $totalHours        = (int)($this->over_stay_time/60); 
            $totalHoursMinutes      = $totalHours * 60;
            $minutes           = ($this->over_stay_time - $totalHoursMinutes); 
            return $totalHours.' H '.$minutes.' Minutes';
        endif;
        return __('parcel.not_check_out');
    }

}
