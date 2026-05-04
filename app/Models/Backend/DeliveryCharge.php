<?php

namespace App\Models\Backend;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\Status;
use App\Models\Backend\Deliverycategory;
use App\Models\Zone;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class DeliveryCharge extends Model
{
    use HasFactory,LogsActivity;


        protected $fillable = [
               
            ];
        protected $guarded = [];

        public function zone(){
            return $this->belongsTo(Zone::class, 'zone_id', 'id');
         }

        public function getActivitylogOptions(): LogOptions
        {

            $logAttributes = [
                'category.name',
                'weight',
                'same_day',
                'next_day',
                'sub_city',
                'outside_city',
                'position',
            ];
            return LogOptions::defaults()
            ->useLogName('DeliveryCharge')
            ->logOnly($logAttributes)
                ->setDescriptionForEvent(fn(string $eventName) => "{$eventName}");
        }


    public function getMyStatusAttribute()
    {
        if($this->status == Status::ACTIVE){
            $status = '<span class="badge badge-pill badge-success">'.trans("status." . $this->status).'</span>';
        }else {
            $status = '<span class="badge badge-pill badge-danger">'.trans("status." . $this->status).'</span>';
        }
        return $status;
    }

     // Get single row in Delivery Category table.
     public function category()
     {
         return $this->belongsTo(Deliverycategory::class, 'category_id', 'id');
     }


    public function fromCountry(){
        return  $this->belongsTo(Country::class,'from_country_id', 'id');
    }

    public function fromCity(){
        return  $this->belongsTo(City::class,'from_city_id', 'id');
    }

    public function fromDistrict(){
        return  $this->belongsTo(District::class,'from_district_id', 'id');
    }

    public function fromTown(){
        return  $this->belongsTo(Town::class,'from_town_id', 'id');
    }
 
    public function toCountry(){
        return  $this->belongsTo(Country::class,'to_country_id', 'id');
    }

    public function toCity(){
        return  $this->belongsTo(City::class,'to_city_id', 'id');
    }

    public function toDistrict(){
        return  $this->belongsTo(District::class,'to_district_id', 'id');
    }

    public function toTown(){
        return  $this->belongsTo(Town::class,'to_town_id', 'id');
    }
    
}
