<?php

namespace App\Models\Backend;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SenderCustomer extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'phone_number',
        'email',
        'whatsapp_number',
        'address',
    ];

    public function getNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getMyAccountTypeAttribute()
    {
        if ($this->account_type == 1) {
            return 'Individual';
        } elseif ($this->account_type == 2) {
            return 'Business';
        }
        return '';
    }

    public function province()
    {
        return $this->belongsTo(Province::class, 'province_id', 'id');
    }
    
    public function city()
    {
        return $this->belongsTo(Province::class, 'city_id', 'id');
    }
}
