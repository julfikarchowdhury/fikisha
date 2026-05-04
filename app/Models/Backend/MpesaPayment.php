<?php

namespace App\Models\Backend;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MpesaPayment extends Model
{
    use HasFactory;

    protected $table = 'mpesa_payments';

    protected $fillable = [
        'merchant_id',
        'parcel_id',
        'checkout_request_id',
        'merchant_request_id',
        'phone',
        'amount',
        'status',
        'parcel_payload',
        'mpesa_response',
        'callback_payload',
    ];

    protected $casts = [
        'parcel_payload' => 'array',
        'mpesa_response' => 'array',
        'callback_payload' => 'array',
    ];

    public function parcel()
    {
        return $this->belongsTo(Parcel::class, 'parcel_id');
    }
}
