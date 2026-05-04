<?php

namespace App\Models\Backend;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiderWithdrawRequest extends Model
{
    use HasFactory;

    protected $table = 'rider_withdraw_requests';

    protected $fillable = [
        'rider_id',
        'amount',
        'status',
        'requested_at',
        'approved_at',
        'processed_by',
        'note',
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function rider()
    {
        return $this->belongsTo(User::class, 'rider_id', 'id');
    }

    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by', 'id');
    }
}
