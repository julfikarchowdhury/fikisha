<?php

namespace App\Models\Backend\FrontWeb;

use App\Enums\DeliveryType; 
use App\Enums\Status;
use App\Models\Backend\ShippingType;
use App\Models\Backend\Upload;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;

class Service extends Model
{
    use HasFactory;

    public function upload()
    {
        return $this->belongsTo(Upload::class, 'image_id', 'id');
    }
    public function scopeActive($query){
        return $query->where('status',Status::ACTIVE);
    }
    public function getImageAttribute()
    {
        if (!empty($this->upload->original['original']) && File::exists(public_path($this->upload->original['original']))) {
            return static_asset($this->upload->original['original']);
        }
        return static_asset('images/default/blank-image.jpg');
    }
 
    public function getMyStatusAttribute(){
        if($this->status == Status::ACTIVE):
            return '<span class="badge badge-success">'.__('status.'.$this->status).'</span>';
        else:
            return '<span class="badge badge-danger">'.__('status.'.$this->status).'</span>';
        endif;
    }


    public function shippingType(){
        return $this->belongsTo(ShippingType::class,'shipping_type_id','id');
    }

    public function getDeliveryTypeAttribute(){
         if($this->delivery_type_id == DeliveryType::SAMEDAY):
            return ('levels.inside_city');
        elseif($this->delivery_type_id == DeliveryType::SUBCITY):
            return ('levels.outside_city');
         endif;
    }
}
