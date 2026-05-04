<?php

namespace App\Models\Backend;

use App\Enums\AccountType;
use App\Enums\ApprovalStatus;
use App\Enums\ParcelStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Models\User;
use App\Models\Backend\Upload;
use App\Enums\Status;
use App\Models\MerchantShops;

class Merchant extends Model
{
    use HasFactory, LogsActivity;
    protected $fillable = [
        'title',
        'account_type',
        'business_name',
        'trade_license',
        'current_balance',
        'rc_number',
        'nif_number',
        'sender_document',
        'sender_document1',
        'user_id'
    ];

    protected $casts = [
        "cod_charges"      => 'array',
    ];

    // Get all row. Descending order using scope.
    public function scopeOrderByDesc($query, $data)
    {
        $query->orderBy($data, 'desc');
    }

    /**
     * Activity Log
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('Merchant')
            ->logOnly(['user.name', 'business_name', 'current_balance'])
            ->setDescriptionForEvent(fn (string $eventName) => "{$eventName}");
    }

    // Get active row this model.
    public function scopeActive($query)
    {
        $query->where('status', Status::ACTIVE);
    }

    // Get single row in User table.
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    // Get single row in Upload table.
    public function licensefile()
    {
        return $this->belongsTo(Upload::class, 'trade_license', 'id');
    }

    public function getTradeAttribute()
    {
        if (!empty($this->licensefile->original['original']) && file_exists(public_path($this->licensefile->original['original']))) {
            return static_asset($this->licensefile->original['original']);
        }
        return static_asset('images/default/not_found.jpg');
    }

    // Get single row in Upload table.
    public function nidfile()
    {
        return $this->belongsTo(Upload::class, 'nid_id', 'id');
    }

    public function getNidAttribute()
    {
        if (!empty($this->nidfile->original['original']) && file_exists(public_path($this->nidfile->original['original']))) {
            return static_asset($this->nidfile->original['original']);
        }
        return static_asset('images/default/not_found.jpg');
    }

    // Get single row in Upload table.
    public function nidBackfile()
    {
        return $this->belongsTo(Upload::class, 'nid_back_id', 'id');
    }

    public function getNidBackAttribute()
    {
        if (!empty($this->nidBackfile->original['original']) && file_exists(public_path($this->nidBackfile->original['original']))) {
            return static_asset($this->nidBackfile->original['original']);
        }
        return static_asset('images/default/not_found.jpg');
    }

    public function upload_doc()
    {
        return $this->belongsTo(Upload::class, 'contract_document', 'id');
    }

    public function getMyContractDocumentAttribute()
    {
        if (!empty($this->upload_doc->original['original']) && file_exists(public_path($this->upload_doc->original['original']))) {
            return static_asset($this->upload_doc->original['original']);
        }
        return '';
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

    public function getMyCodChargesAttribute()
    {
        $data = '';
        foreach ($this->cod_charges as $key => $value) {
            $data .= __('merchant.'.$key) . '= ' . $value . ', ';
        }
        return $data;
    }

    public function parcels()
    {
        return $this->hasMany(Parcel::class, 'merchant_id', 'id');
    }

    public function getDiscountEligibleAttribute()
    {
        $deliveryChargeAmount = $this->parcels->whereIn('status', [ParcelStatus::DELIVERED, ParcelStatus::PARTIAL_DELIVERED, ParcelStatus::RETURN_RECEIVED_BY_MERCHANT])->sum('total_delivery_amount');
        if ($this->minimum_reaches_amount < $deliveryChargeAmount) :
            return true;
        else :
            return false;
        endif;
    }

    public function deliveredParcels()
    {
        return $this->parcels()->whereIn('status', [ParcelStatus::DELIVERED, ParcelStatus::PARTIAL_DELIVERED]);
    }

    public function merchantShops()
    {
        return $this->hasMany(MerchantShops::class, 'merchant_id', 'id');
    }

    public function getActiveShopAttribute()
    {
        return MerchantShops::where(['merchant_id' => $this->id, 'default_shop' => Status::ACTIVE])->first();
    }

    public function totalPayments()
    {
        return $this->hasMany(Payment::class, 'merchant_id', 'id');
    }

    public function totalProcessedPayments()
    {
        return $this->totalPayments()->where('status', ApprovalStatus::PROCESSED);
    }

    public function totalPendingPayments()
    {
        return $this->totalPayments()->where('status', ApprovalStatus::PENDING);
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

    public function merchant_payment_date()
    {
        return $this->hasMany(MerchantPaymentDate::class, 'merchant_id', 'id');
    }
}
