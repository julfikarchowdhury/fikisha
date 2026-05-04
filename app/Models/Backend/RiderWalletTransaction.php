<?php

namespace App\Models\Backend;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiderWalletTransaction extends Model
{
    use HasFactory;

    protected $table = 'rider_wallet_transactions';

    protected $fillable = [
        'rider_id',
        'parcel_id',
        'type',
        'amount',
        'description',
    ];

    public function rider()
    {
        return $this->belongsTo(User::class, 'rider_id', 'id');
    }

    public function parcel()
    {
        return $this->belongsTo(Parcel::class, 'parcel_id', 'id');
    }
}
