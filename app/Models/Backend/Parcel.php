<?php

namespace App\Models\Backend;


use App\Models\MerchantShops;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Models\User;
use App\Models\Backend\Deliverycategory;
use App\Models\Backend\Packaging;
use App\Enums\ParcelStatus;
use App\Models\Backend\Merchantpanel\Invoice;
// use DNS1D;
// use DNS2D; 
use Milon\Barcode\Facades\DNS1DFacade as DNS1D;
use Milon\Barcode\Facades\DNS2DFacade as DNS2D;

use Illuminate\Support\Facades\Auth;

class Parcel extends Model
{
    use HasFactory, LogsActivity;
    protected $fillable = [
        'merchant_id', 'merchant_shop_id', 'pickup_address', 'pickup_phone', 'customer_name', 'customer_phone',
        'customer_address', 'invoice_no', 'category_id', 'weight', 'pickup_date', 'delivery_date', 'packaging_id',
        'selling_price', 'liquid_fragile_amount', 'packaging_amount', 'delivery_charge', 'commission_amount', 'rider_earning',
        'base_delivery_charge', 'receiver_markup', 'final_paid_amount', 'commission_percent', 'platform_total_earning',
        'vat', 'vat_amount', 'total_delivery_amount', 'current_payable', 'parcel_value', 'parcel_file', 'note', 'tracking_id', 'status', 'receiver_mpesa_phone',
        'delivery_proof_image_id', 'delivery_proof_timestamp', 'delivery_proof_lat', 'delivery_proof_lng',
        'created_at', 'updated_at'
    ];

    protected $casts = [
        'cbm_details'   => 'array',
        'receiver_otp_sent_at' => 'datetime',
        'receiver_otp_verified_at' => 'datetime',
    ];

    protected $table = 'parcels';

    public function scopeOrderByDesc($query, $data)
    {
        $query->orderBy($data, 'desc');
    }

    public function items()
    {
        return $this->hasMany(ParcelItem::class, 'parcel_id', 'id');
    }

    public function getIsSmallBatchAttribute()
    {
        if ($this->items()->count() > 0) :
            return '<span class="badge badge-success">' . __('parcel.small_batch') . '</span>';
        else :
            return 'N/A';
        endif;
    }

    /**
     * Activity Log
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('parcel')
            ->logOnly(['merchant.business_name', 'pickup_address', 'pickup_phone', 'customer_name', 'customer_phone', 'customer_address', 'invoice_no', 'selling_price', 'delivery_charge', 'total_delivery_amount', 'current_payable'])
            ->setDescriptionForEvent(fn (string $eventName) => "{$eventName}");
    }

    // Merchant details
    public function merchant()
    {
        return $this->belongsTo(Merchant::class)->with('user');
    }

    public function invoice(){
        return $this->belongsTo(Invoice::class,'invoice_id','id');
    }

    // Merchant details
    public function to_merchant()
    {
        return $this->belongsTo(Merchant::class, 'to_merchant_id', 'id')->with('user');
    }

    // Merchant shop details
    public function merchantShop()
    {
        return $this->belongsTo(MerchantShops::class, 'merchant_shop_id', 'id');
    }

    // Delivery Category details
    public function deliveryCategory()
    {
        return $this->belongsTo(Deliverycategory::class, 'category_id', 'id');
    }

    // Delivery Category details
    public function packaging()
    {
        return $this->belongsTo(Packaging::class);
    }
    public function shop()
    {
        return $this->belongsTo(MerchantShops::class, 'merchant_shop_id', 'id');
    }
    public function parcelEvent()
    {
        return $this->hasMany(ParcelEvent::class, 'parcel_id', 'id');
    }

    public function deliverymanStatement()
    {
        return $this->hasMany(DeliverymanStatement::class, 'parcel_id', 'id');
    }

    public function deliveryman()
    {
        return $this->belongsTo(User::class, 'delivery_man_id', 'id');
    }

    public function getMyItemTypeAttribute()
    {
        $itemType = '';
        foreach (trans("parcelType") as $key => $value) {
            if ($this->item_type == $key) {
                $itemType = $value;
            }
        }
        return $itemType;
    }

    public function getParcelTypeAttribute()
    {
        if ((int)$this->scheduled_amount > 0) {
            $status = '<span class="badge badge-pill badge-success">Booking</span>';
        } else {
            $status = '<span class="badge badge-pill badge-info">Regular</span>';
        }
        return $status;
    }

    public function getParcelStatusAttribute()
    {
        $status = '';
        if ($this->status == ParcelStatus::PENDING) {
            if ((int)$this->scheduled_amount > 0) {
                $status = '<span class="badge badge-pill badge-danger">' . trans("parcelStatusShow." . ParcelStatus::BOOKING) . '</span>';
            } else {
                $status = '<span class="badge badge-pill badge-danger">' . trans("parcelStatusShow." . ParcelStatus::PENDING) . '</span>';
            }
        } elseif ($this->status == ParcelStatus::BOOKING) {
            $status = '<span class="badge badge-pill badge-success">' . trans("parcelStatusShow." . $this->status) . '</span>';
        } elseif ($this->status == ParcelStatus::READY_TO_REASSIGN_REGULAR) {
            $status = '<span class="badge badge-pill badge-info">' . trans("parcelStatusShow." . $this->status) . '</span>';
        } elseif ($this->status == ParcelStatus::READY_TO_REASSIGN_BOOKING) {
            $status = '<span class="badge badge-pill badge-info">' . trans("parcelStatusShow." . $this->status) . '</span>';
        } elseif ($this->status == ParcelStatus::PICKUP_ASSIGN) {
            $status = '<span class="badge badge-pill badge-primary">' . trans("parcelStatusShow." . $this->status) . '</span>';
        } elseif ($this->status == ParcelStatus::CONFIRMED_BOOKING) {
            $status = '<span class="badge badge-pill badge-info">' . trans("parcelStatusShow." . $this->status) . '</span>';
        } elseif ($this->status == ParcelStatus::PROCESSING) {
            $status = '<span class="badge badge-pill badge-info">' . trans("parcelStatusShow." . $this->status) . '</span>';
        } elseif ($this->status == ParcelStatus::RECEIVED_WAREHOUSE) {
            $status = '<span class="badge badge-pill badge-info">' . trans("parcelStatusShow." . $this->status) . '</span>';
        } elseif ($this->status == ParcelStatus::DELIVERY_MAN_ASSIGN) {
            $status = '<span class="badge badge-pill badge-warning">' . trans("parcelStatusShow." . $this->status) . '</span>';
        } elseif ($this->status == ParcelStatus::DELIVERY_RE_SCHEDULE) {
            $status = '<span class="badge badge-pill badge-info">' . trans("parcelStatusShow." . $this->status) . '</span>';
        }
         elseif ($this->status == ParcelStatus::DELIVERY_ATTEMPT1) {
            $status = '<span class="badge badge-pill badge-warning">' . trans("parcelStatusShow." . $this->status) . '</span>';
        }
         elseif ($this->status == ParcelStatus::DELIVERY_ATTEMPT2) {
            $status = '<span class="badge badge-pill badge-warning">' . trans("parcelStatusShow." . $this->status) . '</span>';
        }
         elseif ($this->status == ParcelStatus::DELIVERY_ATTEMPT3) {
            $status = '<span class="badge badge-pill badge-warning">' . trans("parcelStatusShow." . $this->status) . '</span>';
        }
         elseif ($this->status == ParcelStatus::DELIVERY_MAN_ASSIGN1) {
            $status = '<span class="badge badge-pill badge-info">' . trans("parcelStatusShow." . $this->status) . '</span>';
        }
         elseif ($this->status == ParcelStatus::RETURN_TO_COURIER) {
            $status = '<span class="badge badge-pill badge-info">' . trans("parcelStatusShow." . $this->status) . '</span>';
        }
         elseif ($this->status == ParcelStatus::RETURN_ASSIGN_TO_MERCHANT1) {
            $status = '<span class="badge badge-pill badge-info">' . trans("parcelStatusShow." . $this->status) . '</span>';
        }
         elseif ($this->status == ParcelStatus::RETURNING) {
            $status = '<span class="badge badge-pill badge-danger">' . trans("parcelStatusShow." . $this->status) . '</span>';
        }
         elseif ($this->status == ParcelStatus::TRANSIT_SENDING_PROVINCE) {
            $status = '<span class="badge badge-pill badge-secondary">' . trans("parcelStatusShow." . $this->status) .'(' . @$this->fromProvince->name . ')</span>';
        }
         elseif ($this->status == ParcelStatus::RETURN_ASSIGN_TO_MERCHANT) {
            $status = '<span class="badge badge-pill badge-dark">' . trans("parcelStatusShow." . $this->status) . '</span>';
        } elseif ($this->status == ParcelStatus::RETURN_MERCHANT_RE_SCHEDULE) {
            $status = '<span class="badge badge-pill badge-dark">' . trans("parcelStatusShow." . $this->status) . '</span>';
        } elseif ($this->status == ParcelStatus::RETURN_RECEIVED_BY_MERCHANT) {
            $status = '<span class="badge badge-pill badge-success">' . trans("parcelStatusShow." . $this->status) . '</span>';
        } elseif ($this->status == ParcelStatus::DELIVER) {
            $status = '<span class="badge badge-pill badge-success">' . trans("parcelStatusShow." . $this->status) . '</span>';
        } elseif ($this->status == ParcelStatus::DELIVERED) {
            $status = '<span class="badge badge-pill badge-success">' . trans("parcelStatusShow." . $this->status) . '</span>';
        } elseif ($this->status == ParcelStatus::PARTIAL_DELIVERED) {
            $status = '<span class="badge badge-pill badge-success">' . trans("parcelStatusShow." . $this->status) . '</span>';
        } elseif ($this->status == ParcelStatus::RETURN_WAREHOUSE) {
            $status = '<span class="badge badge-pill badge-info">' . trans("parcelStatusShow." . $this->status) . '</span>';
        } elseif ($this->status == ParcelStatus::ASSIGN_MERCHANT) {
            $status = '<span class="badge badge-pill badge-secondary">' . trans("parcelStatusShow." . $this->status) . '</span>';
        } elseif ($this->status == ParcelStatus::RETURNED_MERCHANT) {
            $status = '<span class="badge badge-pill badge-dark">' . trans("parcelStatusShow." . $this->status) . '</span>';
        } elseif ($this->status == ParcelStatus::PICKUP_RE_SCHEDULE) {
            $status = '<span class="badge badge-pill badge-dark">' . trans("parcelStatusShow." . $this->status) . '</span>';
        } elseif ($this->status == ParcelStatus::RECEIVED_BY_PICKUP_MAN) {
            $status = '<span class="badge badge-pill badge-success">' . trans("parcelStatusShow." . $this->status) . '</span>';
        } elseif ($this->status == ParcelStatus::TRANSFER_TO_HUB) {
            $status = '<span class="badge badge-pill badge-info">' . trans("parcelStatusShow." . $this->status) . '</span>' . '<br><span class="badge badge-pill badge-danger mt-1">' . @$this->hub->name . ' To ' . @$this->transferhub->name . '</span>';
        } elseif ($this->status == ParcelStatus::RECEIVED_BY_HUB) {
            $status = '<span class="badge badge-pill badge-info">' . trans("parcelStatusShow." . $this->status) . '</span>';
        } elseif ($this->status == ParcelStatus::DROPPED_OFF_AT_HUB) {
            $status = '<span class="badge badge-pill badge-info">' . trans("parcelStatusShow." . $this->status) . '</span>';
        } elseif ($this->status == ParcelStatus::PARCEL_CANCEL) {
            $status = '<span class="badge badge-pill badge-secondary">' . trans("parcel.cancelled") . '</span>';
        } elseif ($this->status == ParcelStatus::UNCONFIRMED) {
            $status = '<span class="badge badge-pill badge-dark">' . trans("parcelStatusShow." . ParcelStatus::UNCONFIRMED) . '</span>';
        } elseif ($this->status == ParcelStatus::UNCONFIRMED_BOOKING) {
            $status = '<span class="badge badge-pill badge-dark">' . trans("parcelStatusShow." . ParcelStatus::UNCONFIRMED_BOOKING) . '</span>';
        } elseif ($this->status == ParcelStatus::DELIVERY_MAN_ASSIGN_CANCEL) {
            $status = '<span class="badge badge-pill badge-warning">' . trans("parcelStatusShow." . ParcelStatus::DELIVERY_MAN_ASSIGN_CANCEL) . '</span>';
        } elseif ($this->status == ParcelStatus::DELIVERY_RE_SCHEDULE_CANCEL) {
            $status = '<span class="badge badge-pill badge-warning">' . trans("parcelStatusShow." . ParcelStatus::DELIVERY_RE_SCHEDULE_CANCEL) . '</span>';
        } elseif ($this->status == ParcelStatus::CONFIRMED) {
            $status = '<span class="badge badge-pill badge-success">' . trans("parcelStatusShow." . ParcelStatus::CONFIRMED) . '</span>';
        } elseif ($this->status == ParcelStatus::HEADING_TO_PICKUP_POINT) {
            $status = '<span class="badge badge-pill badge-info">' . trans("parcelStatusShow." . ParcelStatus::HEADING_TO_PICKUP_POINT) . '</span>';
        } elseif ($this->status == ParcelStatus::PICKED_UP) {
            $status = '<span class="badge badge-pill badge-warning">' . trans("parcelStatusShow." . ParcelStatus::PICKED_UP) . '</span>';
        } elseif ($this->status == ParcelStatus::HEADING_TO_DROP_OFF) {
            $status = '<span class="badge badge-pill badge-dark">' . trans("parcelStatusShow." . ParcelStatus::HEADING_TO_DROP_OFF) . '</span>';
        } elseif ($this->status == ParcelStatus::HEADING_TO_DROP_OFF_HUB) {
            $status = '<span class="badge badge-pill badge-dark">' . trans("parcelStatusShow." . ParcelStatus::HEADING_TO_DROP_OFF_HUB) . '</span>';
        } elseif ($this->status == ParcelStatus::DROPPED_OFF_HUB2) {
            $status = '<span class="badge badge-pill badge-dark">' . trans("parcelStatusShow." . ParcelStatus::DROPPED_OFF_HUB2) . '(' . @$this->hub->name . ')</span>';
        } elseif ($this->status == ParcelStatus::HEADING_TO_DELIVERY_POINT) {
            $status = '<span class="badge badge-pill badge-warning">' . trans("parcelStatusShow." . ParcelStatus::HEADING_TO_DELIVERY_POINT) . '</span>';
        } elseif ($this->status == ParcelStatus::IMMEDIATE_EXECUTION) {
            $status = '<span class="badge badge-pill badge-dark">' . trans("parcelStatusShow." . ParcelStatus::IMMEDIATE_EXECUTION) . '</span>';
        } 
        elseif ($this->status == ParcelStatus::DROP_OFf_HUB1) {
            $status = '<span class="badge badge-pill badge-warning">' . trans("parcelStatusShow." . ParcelStatus::DROP_OFf_HUB1) . '(' . @$this->hub->name . ')</span>';
        } 
        elseif ($this->status == ParcelStatus::ARRIVED_TO_SENDING_HUB) {
            $status = '<span class="badge badge-pill badge-warning">' . trans("parcelStatusShow." . ParcelStatus::ARRIVED_TO_SENDING_HUB) . '(' . @$this->hub->name . ')</span>';
        } 
        elseif ($this->status == ParcelStatus::TRANSIT_OUT_CITY) {
            $status = '<span class="badge badge-pill badge-dark">' . trans("parcelStatusShow." . ParcelStatus::TRANSIT_OUT_CITY) . '(' . @$this->toProvince->name . ')</span>';
        }
         elseif ($this->status == ParcelStatus::ON_THE_WAY_TO_CITY) {
            $status = '<span class="badge badge-pill badge-info">' . trans("parcelStatusShow." . ParcelStatus::ON_THE_WAY_TO_CITY) . '(' . @$this->toProvince->name . ')</span>';
        } 
         elseif ($this->status == ParcelStatus::ON_THE_WAY_SENDING_PROVINCE) {
            $status = '<span class="badge badge-pill badge-info">' . trans("parcelStatusShow." . ParcelStatus::ON_THE_WAY_SENDING_PROVINCE) . '(' . @$this->fromProvince->name . ')</span>';
        } 
        elseif ($this->status == ParcelStatus::ARRIVED_AT_CITY) {
            $status = '<span class="badge badge-pill badge-warning">' . trans("parcelStatusShow." . ParcelStatus::ARRIVED_AT_CITY) . '(' . @$this->hub->name . ')</span>';
        } elseif ($this->status == ParcelStatus::DROP_OFF_CITY) {
            $status = '<span class="badge badge-pill badge-info">' . trans("parcelStatusShow." . ParcelStatus::DROP_OFF_CITY) . '</span>';
        } elseif ($this->status == ParcelStatus::DELIVERY_FAILURE) {
            $status = '<span class="badge badge-pill badge-warning">' . trans("parcelStatusShow." . $this->status) . '</span>';
        } elseif ($this->status == ParcelStatus::DELIVERY_FAILED) {
            $status = '<span class="badge badge-pill badge-warning">' . trans("parcelStatusShow." . $this->status) . '</span>';
        } elseif ($this->status == ParcelStatus::DELIVERY_MAN_ASSIGN_SECOND_PROVINCE) {
            $status = '<span class="badge badge-pill badge-warning">' . trans("parcelStatusShow." . $this->status) . '</span>';
        } elseif ($this->status == ParcelStatus::MARKETPLACE_PENDING) {
            $status = '<span class="badge badge-pill badge-info">' . trans("parcelStatusShow." . $this->status) . '</span>';
        } elseif ($this->status == ParcelStatus::MARKETPLACE_SENDER_PAYMENT_AWAITING) {
            $status = '<span class="badge badge-pill badge-info">' . trans("parcelStatusShow." . $this->status) . '</span>';
        } elseif ($this->status == ParcelStatus::MARKETPLACE_RECEIVER_PAYMENT_AWAITING) {
            $status = '<span class="badge badge-pill badge-info">' . trans("parcelStatusShow." . $this->status) . '</span>';
        } elseif ($this->status == ParcelStatus::MARKETPLACE_ACCEPTED) {
            $status = '<span class="badge badge-pill badge-info">' . trans("parcelStatusShow." . $this->status) . '</span>';
        } elseif ($this->status == ParcelStatus::MARKETPLACE_PICKED_UP) {
            $status = '<span class="badge badge-pill badge-warning">' . trans("parcelStatusShow." . $this->status) . '</span>';
        } elseif ($this->status == ParcelStatus::MARKETPLACE_DELIVERED) {
            $status = '<span class="badge badge-pill badge-success">' . trans("parcelStatusShow." . $this->status) . '</span>';
        } elseif ($this->status == ParcelStatus::MARKETPLACE_CANCELLED) {
            $status = '<span class="badge badge-pill badge-secondary">' . trans("parcelStatusShow." . $this->status) . '</span>';
        }
        if ($status === '') {
            $status = '<span class="badge badge-pill badge-info">' . trans("parcelStatusShow." . $this->status) . '</span>';
        }
        return $status;
    }

    public function getStatusParcelAttribute($status_id)
    {
        $status = '';
        if ($status_id == ParcelStatus::PENDING) {
            if ((int)$this->scheduled_amount > 0) {
                $status = '<span class="badge badge-pill badge-danger">' . trans("parcelStatusShow." . ParcelStatus::BOOKING) . '</span>';
            } else {
                $status = '<span class="badge badge-pill badge-danger">' . trans("parcelStatusShow." . ParcelStatus::PENDING) . '</span>';
            }
        } elseif ($status_id == ParcelStatus::BOOKING) {
            $status = '<span class="badge badge-pill badge-success">' . trans("parcelStatusShow." . $status_id) . '</span>';
        } elseif ($status_id == ParcelStatus::READY_TO_REASSIGN_REGULAR) {
            $status = '<span class="badge badge-pill badge-info">' . trans("parcelStatusShow." . $status_id) . '</span>';
        } elseif ($status_id == ParcelStatus::READY_TO_REASSIGN_BOOKING) {
            $status = '<span class="badge badge-pill badge-info">' . trans("parcelStatusShow." . $status_id) . '</span>';
        } elseif ($status_id == ParcelStatus::PICKUP_ASSIGN) {
            $status = '<span class="badge badge-pill badge-primary">' . trans("parcelStatusShow." . $status_id) . '</span>';
        } elseif ($status_id == ParcelStatus::CONFIRMED_BOOKING) {
            $status = '<span class="badge badge-pill badge-info">' . trans("parcelStatusShow." . $status_id) . '</span>';
        } elseif ($status_id == ParcelStatus::PROCESSING) {
            $status = '<span class="badge badge-pill badge-info">' . trans("parcelStatusShow." . $status_id) . '</span>';
        } elseif ($status_id == ParcelStatus::DELIVERY_MAN_ASSIGN_CANCEL) {
            $status = '<span class="badge badge-pill badge-warning">' . trans("parcelStatusShow." . $status_id) . '</span>';
        } elseif ($status_id == ParcelStatus::DELIVERY_RE_SCHEDULE_CANCEL) {
            $status = '<span class="badge badge-pill badge-warning">' . trans("parcelStatusShow." . $status_id) . '</span>';
        } elseif ($status_id == ParcelStatus::RECEIVED_WAREHOUSE) {
            $status = '<span class="badge badge-pill badge-info">' . trans("parcelStatusShow." . $status_id) . '</span>';
        } elseif ($status_id == ParcelStatus::DELIVERY_MAN_ASSIGN) {
            $status = '<span class="badge badge-pill badge-warning">' . trans("parcelStatusShow." . $status_id) . '</span>';
        } elseif ($status_id == ParcelStatus::DELIVERY_RE_SCHEDULE) {
            $status = '<span class="badge badge-pill badge-info">' . trans("parcelStatusShow." . $status_id) . '</span>';
        }
         elseif ($status_id == ParcelStatus::DELIVERY_ATTEMPT1) {
            $status = '<span class="badge badge-pill badge-warning">' . trans("parcelStatusShow." . $status_id) . '</span>';
        } 
         elseif ($status_id == ParcelStatus::DELIVERY_ATTEMPT2) {
            $status = '<span class="badge badge-pill badge-warning">' . trans("parcelStatusShow." . $status_id) . '</span>';
        } 
         elseif ($status_id == ParcelStatus::DELIVERY_ATTEMPT3) {
            $status = '<span class="badge badge-pill badge-warning">' . trans("parcelStatusShow." . $status_id) . '</span>';
        } 
         elseif ($status_id == ParcelStatus::DELIVERY_MAN_ASSIGN1) {
            $status = '<span class="badge badge-pill badge-info">' . trans("parcelStatusShow." . $status_id) . '</span>';
        } 
         elseif ($status_id == ParcelStatus::RETURN_TO_COURIER) {
            $status = '<span class="badge badge-pill badge-info">' . trans("parcelStatusShow." . $status_id) . '</span>';
        } 
         elseif ($status_id == ParcelStatus::RETURN_ASSIGN_TO_MERCHANT1) {
            $status = '<span class="badge badge-pill badge-info">' . trans("parcelStatusShow." . $status_id) . '</span>';
        } 
         elseif ($status_id == ParcelStatus::RETURNING) {
            $status = '<span class="badge badge-pill badge-danger">' . trans("parcelStatusShow." . $status_id) . '</span>';
        } 
         elseif ($status_id == ParcelStatus::TRANSIT_SENDING_PROVINCE) {
            $status = '<span class="badge badge-pill badge-secondary">' . trans("parcelStatusShow." . $status_id) .'(' . @$this->fromProvince->name . ')</span>';
        } 
        elseif ($status_id == ParcelStatus::RETURN_ASSIGN_TO_MERCHANT) {
            $status = '<span class="badge badge-pill badge-dark">' . trans("parcelStatusShow." . $status_id) . '</span>';
        } elseif ($status_id == ParcelStatus::RETURN_MERCHANT_RE_SCHEDULE) {
            $status = '<span class="badge badge-pill badge-dark">' . trans("parcelStatusShow." . $status_id) . '</span>';
        } elseif ($status_id == ParcelStatus::RETURN_RECEIVED_BY_MERCHANT) {
            $status = '<span class="badge badge-pill badge-success">' . trans("parcelStatusShow." . $status_id) . '</span>';
        } elseif ($status_id == ParcelStatus::DELIVER) {
            $status = '<span class="badge badge-pill badge-success">' . trans("parcelStatusShow." . $status_id) . '</span>';
        } elseif ($status_id == ParcelStatus::DELIVERED) {
            $status = '<span class="badge badge-pill badge-success">' . trans("parcelStatusShow." . $status_id) . '</span>';
        } elseif ($status_id == ParcelStatus::PARTIAL_DELIVERED) {
            $status = '<span class="badge badge-pill badge-success">' . trans("parcelStatusShow." . $status_id) . '</span>';
        } elseif ($status_id == ParcelStatus::RETURN_WAREHOUSE) {
            $status = '<span class="badge badge-pill badge-info">' . trans("parcelStatusShow." . $status_id) . '</span>';
        } elseif ($status_id == ParcelStatus::ASSIGN_MERCHANT) {
            $status = '<span class="badge badge-pill badge-secondary">' . trans("parcelStatusShow." . $status_id) . '</span>';
        } elseif ($status_id == ParcelStatus::RETURNED_MERCHANT) {
            $status = '<span class="badge badge-pill badge-dark">' . trans("parcelStatusShow." . $status_id) . '</span>';
        } elseif ($status_id == ParcelStatus::PICKUP_RE_SCHEDULE) {
            $status = '<span class="badge badge-pill badge-dark">' . trans("parcelStatusShow." . $status_id) . '</span>';
        } elseif ($status_id == ParcelStatus::RECEIVED_BY_PICKUP_MAN) {
            $status = '<span class="badge badge-pill badge-success">' . trans("parcelStatusShow." . $status_id) . '</span>';
        } elseif ($status_id == ParcelStatus::TRANSFER_TO_HUB) {
            $status = '<span class="badge badge-pill badge-info">' . trans("parcelStatusShow." . $status_id) . '</span>';
        } elseif ($status_id == ParcelStatus::RECEIVED_BY_HUB) {
            $status = '<span class="badge badge-pill badge-info">' . trans("parcelStatusShow." . $status_id) . '</span>';
        } elseif ($status_id == ParcelStatus::DROPPED_OFF_AT_HUB) {
            $status = '<span class="badge badge-pill badge-info">' . trans("parcelStatusShow." . $status_id) . '</span>';
        } elseif ($this->status == ParcelStatus::PARCEL_CANCEL) {
            $status = '<span class="badge badge-pill badge-secondary">' . trans("parcel.cancelled") . '</span>';
        } elseif ($this->status == ParcelStatus::UNCONFIRMED) {
            $status = '<span class="badge badge-pill badge-dark">' . trans("parcelStatusShow." . ParcelStatus::UNCONFIRMED) . '</span>';
        } elseif ($this->status == ParcelStatus::UNCONFIRMED_BOOKING) {
            $status = '<span class="badge badge-pill badge-dark">' . trans("parcelStatusShow." . ParcelStatus::UNCONFIRMED_BOOKING) . '</span>';
        } elseif ($this->status == ParcelStatus::CONFIRMED) {
            $status = '<span class="badge badge-pill badge-success">' . trans("parcelStatusShow." . ParcelStatus::CONFIRMED) . '</span>';
        } elseif ($this->status == ParcelStatus::HEADING_TO_PICKUP_POINT) {
            $status = '<span class="badge badge-pill badge-info">' . trans("parcelStatusShow." . ParcelStatus::HEADING_TO_PICKUP_POINT) . '</span>';
        } elseif ($this->status == ParcelStatus::PICKED_UP) {
            $status = '<span class="badge badge-pill badge-warning">' . trans("parcelStatusShow." . ParcelStatus::PICKED_UP) . '</span>';
        } elseif ($this->status == ParcelStatus::HEADING_TO_DROP_OFF_HUB) {
            $status = '<span class="badge badge-pill badge-info">' . trans("parcelStatusShow." . ParcelStatus::HEADING_TO_DROP_OFF_HUB) . '</span>';
        } elseif ($this->status == ParcelStatus::HEADING_TO_DELIVERY_POINT) {
            $status = '<span class="badge badge-pill badge-warning">' . trans("parcelStatusShow." . ParcelStatus::HEADING_TO_DELIVERY_POINT) . '</span>';
        } elseif ($this->status == ParcelStatus::IMMEDIATE_EXECUTION) {
            $status = '<span class="badge badge-pill badge-dark">' . trans("parcelStatusShow." . ParcelStatus::IMMEDIATE_EXECUTION) . '</span>';
        } 
        elseif ($this->status == ParcelStatus::DROP_OFf_HUB1) {
            $status = '<span class="badge badge-pill badge-warning">' . trans("parcelStatusShow." . ParcelStatus::DROP_OFf_HUB1) . '(' . @$this->hub->name . ')</span>';
        } 
        elseif ($this->status == ParcelStatus::ARRIVED_TO_SENDING_HUB) {
            $status = '<span class="badge badge-pill badge-warning">' . trans("parcelStatusShow." . ParcelStatus::ARRIVED_TO_SENDING_HUB) . '(' . @$this->hub->name . ')</span>';
        } 
        elseif ($this->status == ParcelStatus::HEADING_TO_DROP_OFF) {
            $status = '<span class="badge badge-pill badge-warning">' . trans("parcelStatusShow." . ParcelStatus::HEADING_TO_DROP_OFF) . '</span>';
        } elseif ($this->status == ParcelStatus::DROPPED_OFF_HUB2) {
            $status = '<span class="badge badge-pill badge-warning">' . trans("parcelStatusShow." . ParcelStatus::DROPPED_OFF_HUB2) . '(' . @$this->transferhub->name . ')</span>';
        } elseif ($this->status == ParcelStatus::TRANSIT_OUT_CITY) {
            $status = '<span class="badge badge-pill badge-dark">' . trans("parcelStatusShow." . ParcelStatus::TRANSIT_OUT_CITY) . '(' . @$this->toProvince->name . ')</span>';
        } 
        elseif ($this->status == ParcelStatus::ON_THE_WAY_TO_CITY) {
            $status = '<span class="badge badge-pill badge-info">' . trans("parcelStatusShow." . ParcelStatus::ON_THE_WAY_TO_CITY) . '(' . @$this->toProvince->name . ')</span>';
        } 
        elseif ($this->status == ParcelStatus::ON_THE_WAY_SENDING_PROVINCE) {
            $status = '<span class="badge badge-pill badge-info">' . trans("parcelStatusShow." . ParcelStatus::ON_THE_WAY_SENDING_PROVINCE) . '(' . @$this->fromProvince->name . ')</span>';
        } 
        elseif ($this->status == ParcelStatus::ARRIVED_AT_CITY) {
            $status = '<span class="badge badge-pill badge-warning">' . trans("parcelStatusShow." . ParcelStatus::ARRIVED_AT_CITY) . '(' . @$this->transferhub->name . ')</span>';
        } elseif ($this->status == ParcelStatus::DROP_OFF_CITY) {
            $status = '<span class="badge badge-pill badge-info">' . trans("parcelStatusShow." . ParcelStatus::DROP_OFF_CITY) . '</span>';
        } elseif ($this->status == ParcelStatus::DELIVERY_FAILURE) {
            $status = '<span class="badge badge-pill badge-warning">' . trans("parcelStatusShow." . ParcelStatus::DROP_OFF_CITY) . '</span>';
        } elseif ($this->status == ParcelStatus::DELIVERY_FAILED) {
            $status = '<span class="badge badge-pill badge-warning">' . trans("parcelStatusShow." . ParcelStatus::DROP_OFF_CITY) . '</span>';
        } elseif ($this->status == ParcelStatus::DELIVERY_MAN_ASSIGN_SECOND_PROVINCE) {
            $status = '<span class="badge badge-pill badge-warning">' . trans("parcelStatusShow." . ParcelStatus::DELIVERY_MAN_ASSIGN_SECOND_PROVINCE) . '</span>';
        }
        return $status;
    }


    public function getMyPriorityAttribute()
    {
        $priority = '';
        if ($this->priority_type_id == 1) {
            $priority = __('parcel.high');
        } else {
            $priority = __('parcel.normal');
        }
        return $priority;
    }

    public function getBarcodePrintAttribute()
    {
        return DNS1D::getBarcodeHTML($this->tracking_id, 'C128');
    }

    public function getQrcodePrintAttribute()
    {
        return 'data:image/png;base64,' . DNS2D::getBarcodePNG(url('/tracking/qr', $this->tracking_id), 'QRCODE', 10, 10, array(1, 1, 1), false);
    }

    public function getStatusNameAttribute()
    {
        return __('parcelStatus.' . $this->status);
    }

    

    public function deliveryCharge()
    {
        return $this->belongsTo(DeliveryCharge::class, 'delivery_charge_id', 'id');
    }


    public function parcelCategory()
    {
        return $this->belongsTo(ParcelCategory::class, 'parcel_category_id', 'id');
    }

    public function toTown()
    {
        return $this->belongsTo(Town::class, 'to_state_id', 'id');
    }

    public function fromTown()
    {
        return $this->belongsTo(Town::class, 'from_state_id', 'id');
    }

    public function toProvince()
    {
        return $this->belongsTo(Province::class, 'to_state_id', 'id');
    }

    public function toCity()
    {
        return $this->belongsTo(City::class, 'to_city_id', 'id');
    }

    public function fromProvince()
    {
        return $this->belongsTo(Province::class, 'from_state_id', 'id');
    }

    public function fromCity()
    {
        return $this->belongsTo(City::class, 'from_city_id', 'id');
    }

    public function toMerchant()
    {
        return $this->belongsTo(Merchant::class, 'to_merchant', 'id')->with('user');
    }

    public function customer()
    {
        return $this->belongsTo(SenderCustomer::class, 'customer_id', 'id');
    }
}
