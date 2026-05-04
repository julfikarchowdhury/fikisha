<?php

namespace App\Models\Backend;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParcelItem extends Model
{

    use HasFactory;
 
    protected $guarded = [];

    public function parcel()
    {
        return $this->belongsTo(Parcel::class, 'parcel_id', 'id');
    }

    public function category()
    {
        return $this->belongsTo(ParcelCategory::class, 'category_id', 'id');
    }

    public function packaging()
    {
        return $this->belongsTo(Packaging::class, 'packaging_id', 'id');
    }

    public function getTotalValumetricWeightAttribute(){
        $dimensions = $this->length * $this->width * $this->height;
        return ($dimensions/500);
        
        
    }
}
