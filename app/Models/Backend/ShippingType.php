<?php

namespace App\Models\Backend;

use App\Enums\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingType extends Model
{
    use HasFactory;
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'delivery_type_id',
        'title',
        'basic_price',
        'start_weight',
        'end_weight',
        'addi_weight_price',
        'start_volume',
        'end_volume',
        'addi_volume_price',
        'start_distance',
        'end_distance',
        'addi_distance_price',
        'slots'
    ];

    public function scopeActive($query)
    {
        $query->where('status', Status::ACTIVE);
    }
    public function getMyStatusAttribute()
    {
        if ($this->status == Status::ACTIVE) {
            $status = '<span class="badge badge-pill badge-success">' . trans("status." . $this->status) . '</span>';
        } else {
            $status = '<span class="badge badge-pill badge-danger">' . trans("status." . $this->status) . '</span>';
        }
        return $status;
    }


    public function deliveryType()
    {
        return $this->belongsTo(DeliveryType::class, 'delivery_type_id', 'id');
    }

    public function ShippingChargeOptions()
    {
        return $this->hasMany(ShippingChargeOption::class, 'shipping_type_id', 'id');
    }

    public function getBasicWeightFrameValueAttribute()
    {
        return ($this->end_weight - $this->start_weight);
    }

    public function getBasicVolumeFrameValueAttribute()
    {
        return ($this->end_volume - $this->start_volume);
    }

    public function getBasicDistanceFrameValueAttribute()
    {
        return ($this->end_distance - $this->start_distance);
    }
}
