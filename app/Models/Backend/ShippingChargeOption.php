<?php

namespace App\Models\Backend;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Backend\ShippingType;
class ShippingChargeOption extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function shippingType(){
        return $this->belongsTo(ShippingType::class,'shipping_type_id','id');
    }
}
