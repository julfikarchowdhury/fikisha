<?php

namespace App\Models\Backend;

use Illuminate\Database\Eloquent\Model;

class RiderLocation extends Model
{
    protected $fillable = [
        'rider_id',
        'parcel_id',
        'lat',
        'lng',
    ];
}

