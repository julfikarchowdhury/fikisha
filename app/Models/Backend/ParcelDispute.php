<?php

namespace App\Models\Backend;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParcelDispute extends Model
{
    use HasFactory;

    protected $fillable = [
        'parcel_id',
        'raised_by',
        'reason_type',
        'description',
        'evidence_files',
        'status',
        'admin_decision',
        'liability',
        'refund_amount',
        'rider_liability_amount',
        'refund_method',
        'refund_reference_id',
        'refund_processed_by',
        'refund_processed_at',
        'refund_status',
        'refund_note',
        'resolved_at',
    ];

    protected $casts = [
        'evidence_files' => 'array',
        'resolved_at' => 'datetime',
        'refund_processed_at' => 'datetime',
    ];

    public function parcel()
    {
        return $this->belongsTo(Parcel::class);
    }
}
