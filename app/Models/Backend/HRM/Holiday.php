<?php

namespace App\Models\Backend\HRM;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    use HasFactory;
    public function getTotalDaysAttribute(){
        $from  = Carbon::parse($this->from)->startOfDay();
        $to    = Carbon::parse($this->to)->endOfDay()->addMinute(1); 

        return Carbon::parse($from)->diffInDays($to);
    }
}
