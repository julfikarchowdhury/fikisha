<?php

namespace App\Models\Backend;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourierStatement extends Model
{
    use HasFactory;
    public function parcel(){
        return $this->belongsTo(Parcel::class,'parcel_id','id');
    }
}
