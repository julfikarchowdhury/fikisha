<?php

namespace App\Models\Backend\HRM;

use App\Enums\WeekendStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Weekend extends Model
{
    use HasFactory;
    public function getMyStatusAttribute(){
        if($this->is_weekend == WeekendStatus::YES):
            return '<span class="badge badge-danger">'.__('parcel.yes').'</span>';
        elseif($this->is_weekend == WeekendStatus::NO):
            return '<span class="badge badge-success">'.__('parcel.no').'</span>';
        endif;
    }
}
