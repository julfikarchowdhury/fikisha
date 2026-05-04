<?php

namespace App\Models\Backend;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiderBankAccount extends Model
{
    use HasFactory;

    protected $table = 'rider_bank_accounts';

    protected $fillable = [
        'rider_id',
        'bank_name',
        'account_name',
        'account_number',
        'mobile_wallet_number',
        'routing_number',
    ];

    public function rider()
    {
        return $this->belongsTo(User::class, 'rider_id', 'id');
    }
}
