<?php

namespace App\Models\Backend;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiderWallet extends Model
{
    use HasFactory;

    protected $table = 'rider_wallets';

    protected $fillable = [
        'rider_id',
        'balance',
        'pending_withdraw_amount',
    ];

    public function rider()
    {
        return $this->belongsTo(User::class, 'rider_id', 'id');
    }

    public function transactions()
    {
        return $this->hasMany(RiderWalletTransaction::class, 'rider_id', 'rider_id');
    }

    public function withdrawRequests()
    {
        return $this->hasMany(RiderWithdrawRequest::class, 'rider_id', 'rider_id');
    }
}
