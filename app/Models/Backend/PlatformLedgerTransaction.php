<?php

namespace App\Models\Backend;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlatformLedgerTransaction extends Model
{
    use HasFactory;

    protected $table = 'platform_ledger_transactions';

    protected $fillable = [
        'parcel_id',
        'type',
        'direction',
        'amount',
        'reference_id',
        'description',
    ];
}
