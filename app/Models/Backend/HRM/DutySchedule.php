<?php

namespace App\Models\Backend\HRM;

use App\Models\Backend\Role;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DutySchedule extends Model
{
    use HasFactory;
    public function role(){
        return $this->belongsTo(Role::class,'role_id','id');
    }
 
    public function getStaytimeAttribute(){ 
        $totalHours        = Carbon::parse($this->start_time)->diffInHours($this->end_time);
        $totalHoursMinutes = $totalHours * 60; 
        $totalMinutes      = Carbon::parse($this->start_time)->diffInMinutes($this->end_time);
        $minutes           = ($totalMinutes - $totalHoursMinutes);
        return $totalHours.' H '.$minutes.' Minutes'; 
    }
}
