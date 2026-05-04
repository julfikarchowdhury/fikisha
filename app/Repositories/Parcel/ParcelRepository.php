<?php

namespace App\Repositories\Parcel;

use App\Enums\BooleanStatus;
use App\Enums\ParcelStatus;
use App\Enums\InvoiceStatus;
use App\Enums\WhoPays;
use App\Enums\DeliveryType;
use App\Enums\DeliveryTime;
use App\Enums\SmsSendStatus;
use App\Enums\StatementType;
use App\Enums\Status;
use App\Enums\UserType;
use App\Helpers\DeliveryChargeHelper;
use App\Http\Services\PushNotificationService;
use App\Http\Services\SmsService;
use App\Models\Backend\Deliverycategory;
use App\Models\Backend\DeliveryCharge;
use App\Models\Backend\DeliveryMan;
use App\Models\Backend\DeliverymanStatement;
use App\Models\Backend\Merchant;
use App\Models\Backend\Packaging;
use App\Models\Backend\Parcel;
use App\Models\Backend\ParcelEvent;
use App\Models\Backend\CourierStatement;
use App\Models\Backend\MerchantDeliveryCharge;
use App\Models\Backend\MerchantStatement;
use App\Models\Backend\ParcelItem;
use App\Models\Backend\VatStatement;
use App\Repositories\Parcel\ParcelInterface;
use Carbon\Carbon;
use App\Models\Backend\ParcelLogs;
use App\Models\Backend\Upload;
use App\Models\Backend\Receiver;
use App\Models\Backend\SenderCustomer;
use App\Models\Backend\ShippingType;
use App\Models\Config;
use App\Services\MpesaStkPushService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\MerchantShops;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ParcelRepository implements ParcelInterface
{

    public function all()
    {
        return Parcel::with('parcelEvent')
            ->orderBy('priority_type_id')
            ->orderBy('id', 'desc')
            ->paginate(10);
    }

    public function parcelReturnReport($request)
    {
        $parcels = Parcel::with('parcelEvent')
            ->orderBy('priority_type_id')
            ->orderBy('id', 'desc')
            ->where(function ($query) use ($request) {
                if ($request->parcel_date) {
                    $date = explode('To', $request->parcel_date);
                    if (is_array($date)) {
                        $from   = Carbon::parse(trim($date[0]))->startOfDay()->toDateTimeString();
                        $to     = Carbon::parse(trim($date[1]))->endOfDay()->toDateTimeString();
                        $query->whereBetween('created_at', [$from, $to]);
                    }
                }


                if ($request->from_province_id) {
                    $query->where('from_state_id', $request->from_province_id);
                }

                if ($request->to_province_id) {
                    $query->where('to_state_id', $request->to_province_id);
                }

                // delivery_type_id and shipping_type removed (online-only marketplace flow)

                if ($request->parcel_merchant_id) {
                    $query->where('merchant_id', $request->parcel_merchant_id);
                }
            })
            ->where('status', ParcelStatus::RETURN_RECEIVED_BY_MERCHANT)
            ->paginate(10);
        return $parcels;
    }

    public function deliveryManParcel()
    {
        return Parcel::orderByDesc('id')->where(function ($query) {
            if (auth()->user()->deliveryman) {
                $query->whereHas('parcelEvent', function ($queryParcelEvent) {
                    if (auth()->user()->deliveryman->id) {
                        $queryParcelEvent->where(['delivery_man_id' => auth()->user()->deliveryman->id]);
                        $queryParcelEvent->orWhere(['pickup_man_id' => auth()->user()->deliveryman->id]);
                    }
                });
            }
        })->get();
    }

    public function deliveryTypes()
    {
        $types = [
            'same_day',
            'next_day',
            'sub_city',
            'outside_City',
        ];
        return Config::whereIn('key', $types)->where('value', 1)->get();
    }

    public function filter($request)
    {
        return Parcel::with('parcelEvent')
            ->orderByDesc('id')
            ->where(function ($query) use ($request) {
                if ($request->parcel_date) {
                    $date = explode('To', $request->parcel_date);
                    if (is_array($date)) {
                        $from   = Carbon::parse(trim($date[0]))->startOfDay()->toDateTimeString();
                        $to     = Carbon::parse(trim($date[1]))->endOfDay()->toDateTimeString();
                        $query->whereBetween('created_at', [$from, $to]);
                    }
                }


                if ($request->from_province_id) {
                    $query->where('from_state_id', $request->from_province_id);
                }

                if ($request->to_province_id) {
                    $query->where('to_state_id', $request->to_province_id);
                }

                // delivery_type_id and shipping_type removed (online-only marketplace flow)

                if ($request->parcel_status) {
                    if ($request->parcel_status == ParcelStatus::PENDING) {
                        $query->whereIn('status', [
                            ParcelStatus::PENDING,
                            ParcelStatus::DELIVERY_RE_SCHEDULE,
                            ParcelStatus::DELIVERY_MAN_ASSIGN_CANCEL,
                            ParcelStatus::DELIVERY_RE_SCHEDULE_CANCEL,
                        ]);
                    } elseif ($request->parcel_status == ParcelStatus::DELIVERY_MAN_ASSIGN) {
                        $query->whereIn('status', [
                            ParcelStatus::DELIVERY_MAN_ASSIGN,
                            ParcelStatus::DELIVERY_RE_SCHEDULE,
                            ParcelStatus::CONFIRMED,
                            ParcelStatus::CONFIRMED_BOOKING,
                            ParcelStatus::UNCONFIRMED,
                            ParcelStatus::UNCONFIRMED_BOOKING,
                        ]);
                    } elseif ($request->parcel_status == ParcelStatus::PROCESSING) {
                        $query->whereIn('status', [
                            ParcelStatus::PROCESSING,
                            ParcelStatus::HEADING_TO_PICKUP_POINT,
                            ParcelStatus::PICKED_UP,
                            ParcelStatus::HEADING_TO_DELIVERY_POINT,
                            ParcelStatus::DROP_OFF_CITY,
                            ParcelStatus::DROP_OFf_HUB1,
                            ParcelStatus::HEADING_TO_DROP_OFF,
                            ParcelStatus::TRANSIT_OUT_CITY,
                            ParcelStatus::ON_THE_WAY_TO_CITY,
                            ParcelStatus::ARRIVED_AT_CITY,
                            ParcelStatus::DELIVERY_MAN_ASSIGN_SECOND_PROVINCE,
                        ]);
                    } elseif ($request->parcel_status == ParcelStatus::DELIVERED) {
                        $query->whereIn('status', [
                            ParcelStatus::DELIVERED,
                            ParcelStatus::PARTIAL_DELIVERED
                        ]);
                    } elseif ($request->parcel_status == ParcelStatus::DELIVERY_FAILURE) {
                        $query->whereIn('status', [
                            ParcelStatus::DELIVERY_FAILURE,
                            ParcelStatus::DELIVERY_FAILED,
                            ParcelStatus::PARCEL_CANCEL,
                            ParcelStatus::DELIVERY_ATTEMPT1,
                            ParcelStatus::DELIVERY_ATTEMPT2,
                            ParcelStatus::DELIVERY_ATTEMPT3,
                            ParcelStatus::DELIVERY_MAN_ASSIGN1,
                            ParcelStatus::RETURN_TO_COURIER,
                            ParcelStatus::RETURNING,
                            ParcelStatus::TRANSIT_SENDING_PROVINCE,
                            ParcelStatus::ON_THE_WAY_SENDING_PROVINCE,
                            ParcelStatus::ARRIVED_TO_SENDING_HUB,
                            ParcelStatus::RETURN_ASSIGN_TO_MERCHANT,
                            ParcelStatus::RETURN_ASSIGN_TO_MERCHANT1,
                            ParcelStatus::RETURN_MERCHANT_RE_SCHEDULE,
                            ParcelStatus::RETURN_RECEIVED_BY_MERCHANT,
                        ]);
                    } else {
                        $query->where('status', $request->parcel_status);
                    }
                }

                if ($request->parcel_merchant_id) {
                    $query->where('merchant_id', $request->parcel_merchant_id);
                }
                if ($request->payment_status) {
                    $query->where('payment_status', $request->payment_status);
                }
            })->paginate(10);
    }

    public function filterPrint($request)
    {
        return Parcel::with('parcelEvent')
            ->orderByDesc('id')
            ->where(function ($query) use ($request) {
                if ($request->parcel_date) {
                    $date = explode('To', $request->parcel_date);
                    if (is_array($date)) {
                        $from   = Carbon::parse(trim($date[0]))->startOfDay()->toDateTimeString();
                        $to     = Carbon::parse(trim($date[1]))->endOfDay()->toDateTimeString();
                        $query->whereBetween('created_at', [$from, $to]);
                    }
                }


                if ($request->from_province_id) {
                    $query->where('from_state_id', $request->from_province_id);
                }

                if ($request->to_province_id) {
                    $query->where('to_state_id', $request->to_province_id);
                }

                // delivery_type_id and shipping_type removed (online-only marketplace flow)

                if ($request->parcel_status) {
                    if ($request->parcel_status == ParcelStatus::PENDING) {
                        $query->whereIn('status', [
                            ParcelStatus::PENDING,
                            ParcelStatus::DELIVERY_RE_SCHEDULE,
                            ParcelStatus::DELIVERY_MAN_ASSIGN_CANCEL,
                            ParcelStatus::DELIVERY_RE_SCHEDULE_CANCEL,
                        ]);
                    } elseif ($request->parcel_status == ParcelStatus::DELIVERY_MAN_ASSIGN) {
                        $query->whereIn('status', [
                            ParcelStatus::DELIVERY_MAN_ASSIGN,
                            ParcelStatus::DELIVERY_RE_SCHEDULE,
                            ParcelStatus::CONFIRMED,
                            ParcelStatus::CONFIRMED_BOOKING,
                            ParcelStatus::UNCONFIRMED,
                            ParcelStatus::UNCONFIRMED_BOOKING,
                        ]);
                    } elseif ($request->parcel_status == ParcelStatus::PROCESSING) {
                        $query->whereIn('status', [
                            ParcelStatus::PROCESSING,
                            ParcelStatus::HEADING_TO_PICKUP_POINT,
                            ParcelStatus::PICKED_UP,
                            ParcelStatus::HEADING_TO_DELIVERY_POINT,
                            ParcelStatus::DROP_OFF_CITY,
                            ParcelStatus::DROP_OFf_HUB1,
                            ParcelStatus::HEADING_TO_DROP_OFF,
                            ParcelStatus::TRANSIT_OUT_CITY,
                            ParcelStatus::ON_THE_WAY_TO_CITY,
                            ParcelStatus::ARRIVED_AT_CITY,
                            ParcelStatus::DELIVERY_MAN_ASSIGN_SECOND_PROVINCE,
                        ]);
                    } elseif ($request->parcel_status == ParcelStatus::DELIVERED) {
                        $query->whereIn('status', [
                            ParcelStatus::DELIVERED,
                            ParcelStatus::PARTIAL_DELIVERED
                        ]);
                    } elseif ($request->parcel_status == ParcelStatus::DELIVERY_FAILURE) {
                        $query->whereIn('status', [
                            ParcelStatus::DELIVERY_FAILURE,
                            ParcelStatus::DELIVERY_FAILED,
                            ParcelStatus::PARCEL_CANCEL,
                            ParcelStatus::DELIVERY_ATTEMPT1,
                            ParcelStatus::DELIVERY_ATTEMPT2,
                            ParcelStatus::DELIVERY_ATTEMPT3,
                            ParcelStatus::DELIVERY_MAN_ASSIGN1,
                            ParcelStatus::RETURN_TO_COURIER,
                            ParcelStatus::RETURNING,
                            ParcelStatus::TRANSIT_SENDING_PROVINCE,
                            ParcelStatus::ON_THE_WAY_SENDING_PROVINCE,
                            ParcelStatus::ARRIVED_TO_SENDING_HUB,
                            ParcelStatus::RETURN_ASSIGN_TO_MERCHANT,
                            ParcelStatus::RETURN_ASSIGN_TO_MERCHANT1,
                            ParcelStatus::RETURN_MERCHANT_RE_SCHEDULE,
                            ParcelStatus::RETURN_RECEIVED_BY_MERCHANT,
                        ]);
                    } else {
                        $query->where('status', $request->parcel_status);
                    }
                }

                if ($request->parcel_merchant_id) {
                    $query->where('merchant_id', $request->parcel_merchant_id);
                }
                if ($request->payment_status) {
                    $query->where('payment_status', $request->payment_status);
                }
            })->get();
    }

    public function get($id)
    {
        return Parcel::where(['id' => $id])
            ->with('merchant', 'merchant.user', 'merchantShop', 'deliveryCategory', 'packaging')
            ->first();
    }


    public function parcelEvents($id)
    {
        return ParcelEvent::with(['deliveryMan', 'pickupman', 'transferDeliveryman', 'user'])
            ->where('parcel_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function parcelTracking($request)
    {
        $trackingValue = trim((string) ($request->tracking_id ?? ''));
        if ($trackingValue === '') {
            return null;
        }

        return Parcel::query()
            ->where('tracking_id', $trackingValue)
            ->orWhere('tracking_token', $trackingValue)
            ->first();
    }

    public function parcelTrackingQr($tracking_id)
    {
        $trackingValue = trim((string) $tracking_id);
        if ($trackingValue === '') {
            return null;
        }

        return Parcel::query()
            ->where('tracking_id', $trackingValue)
            ->orWhere('tracking_token', $trackingValue)
            ->first();
    }

    public function details($id)
    {
        return Parcel::where(['id' => $id])
            ->with('merchant', 'merchant.user', 'merchantShop', 'deliveryCategory', 'packaging')
            ->first();
    }

    public function statusUpdate($id, $status_id)
    {
        $parcel = Parcel::find($id);
        $parcel->status = $status_id;
        $parcel->save();
        return true;
    }

    public function ParcelStatusUpdate($id, $status_id, $request)
    {
        try {
            $parcel = Parcel::find($id);
            $parcel->status = $status_id;
            if ($status_id == ParcelStatus::RETURN_ASSIGN_TO_MERCHANT1) {
                $parcel->delivery_date = $request->date;
            }
            $parcel->save();

            $event                           = new ParcelEvent();
            $event->parcel_id                = $parcel->id;
            if ($request->who) :
                $event->who                  = $request->who;
            endif;
            if ($status_id == ParcelStatus::DELIVERY_MAN_ASSIGN1 || $status_id == ParcelStatus::DELIVERY_MAN_ASSIGN || $status_id == ParcelStatus::DELIVERY_RE_SCHEDULE) {
                $event->delivery_man_id      = $request->delivery_man_id;
                $parcel->delivery_man_id      = $request->delivery_man_id;
                $parcel->save();
            }
            $event->note                     = $request->note;
            $event->parcel_status            = $status_id;
            $event->created_by               = Auth::user()->id;
            $event->save();
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function ParcelCancel($request)
    {
        try {
            $parcel         = Parcel::find($request->parcel_id);
            $parcel->status = ParcelStatus::PARCEL_CANCEL;
            $parcel->save();
            $cancelEvent                           = new ParcelEvent();
            $cancelEvent->parcel_id                = $parcel->id;
            $cancelEvent->note                     = $request->note;
            $cancelEvent->who                     = $request->who;
            $cancelEvent->parcel_status            = ParcelStatus::PARCEL_CANCEL;
            $cancelEvent->created_by               = Auth::user()->id;
            $cancelEvent->save();
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function deliveryCharges()
    {
        return DeliveryCharge::distinct('category_id')->pluck('category_id');
    }

    public function deliveryCategories()
    {
        return pluck(Deliverycategory::all(), 'obj', 'id');
    }

    public function packaging()
    {
        return Packaging::where('status', Status::ACTIVE)->get();
    }

    public function store($request)
    {
        try {
            DB::beginTransaction();
            $merchant                       = $request->merchant_id
                ? Merchant::with('user')->find($request->merchant_id)
                : null;
            $chargeDetails                  = json_decode($request->chargeDetails);

            $parcel                         = new Parcel();
            $parcel->merchant_id            = $request->merchant_id ?? null;
            $parcel->sender_company_name  = $request->company_name;
            $parcel->sender_first_name    = $request->first_name;
            $parcel->sender_last_name     = $request->last_name;
            $parcel->sender_email         = $request->sender_email;
            $parcel->sender_phone         = $request->pickup_phone;
            $parcel->sender_residential_address = $request->residential_address;
            $parcel->category_id            = $request->category_id;
            if ($request->weight !== "") {
                $parcel->weight                 = $request->weight;
            }

            if ($request->selling_price) {
                $parcel->selling_price          = $request->selling_price;
            }

            if ($request->parcel_value) {
                $parcel->parcel_value = $request->parcel_value;
            }

            if ($request->hasFile('parcel_file')) {
                $file = $request->file('parcel_file');
                $destinationPath = public_path('uploads/parcel');
                if (!File::exists($destinationPath)) {
                    File::makeDirectory($destinationPath, 0755, true);
                }
                $fileName = date('YmdHis') . '.' . $file->getClientOriginalExtension();
                $file->move($destinationPath, $fileName);
                $parcel->parcel_file = 'uploads/parcel/' . $fileName;
            }

            $parcel->from_state_id        = $request->from_state_id;
            $parcel->from_account_type    = 1;
            $parcel->from_point_type      = $request->from_point_type ?? 1;

            if ($request->from_point_type == 1) {
                $parcel->from_town            = $request->from_town;
                $parcel->from_building        = $request->from_building;
                $parcel->from_city_id         = $request->from_city_id;
                $parcel->from_portal_code     = $request->from_portal_code;
            } elseif ($request->from_point_type == 2) {
                $parcel->from_town            = null;
                $parcel->from_building        = null;
                $parcel->from_city_id         = null;
                $parcel->from_portal_code     = null;
            }


            $parcel->to_state_id          = $request->to_state_id;
            $parcel->to_account_type      = 1;
            $parcel->to_point_type        = $request->to_point_type ?? 1;
            $parcel->status               = ParcelStatus::MARKETPLACE_PENDING;
            if ($request->to_point_type == 1) {
                $parcel->to_town              = $request->to_town;
                $parcel->to_building          = $request->to_building;
                $parcel->to_city_id           = $request->to_city_id;
                $parcel->to_portal_code       = $request->to_portal_code;
            } elseif ($request->to_point_type == 2) {
                $parcel->to_town              = null;
                $parcel->to_building          = null;
                $parcel->to_city_id           = null;
                $parcel->to_portal_code       = null;
            }


            if ($request->from_whatsapp_number) :
                $parcel->from_whatsapp_number = $request->from_whatsapp_number;
            elseif ($request->hub_from_whatsapp_number) :
                $parcel->from_whatsapp_number = $request->hub_from_whatsapp_number;
            endif;
            $parcel->to_whatsapp_number   = $request->to_whatsapp_number;
            $parcel->receiver_whatsapp_number   = $request->receiver_whatsapp_number;

            $parcel->merchant_shop_id       = null;
            $parcel->pickup_phone           = $request->pickup_phone;
            $parcel->pickup_address         = $request->pickup_address;

            $parcel->customer_company_name  = $request->customer_company_name;
            $parcel->customer_first_name    = $request->customer_first_name;
            $parcel->customer_last_name     = $request->customer_last_name;
            $parcel->customer_name          = $request->customer_name;
            $parcel->receiver_email         = $request->receiver_email;
            $parcel->customer_phone         = $request->customer_phone;
            $parcel->customer_address       = $request->customer_address;

            $parcel->to_merchant_id = null;
            $parcel->customer_id = $request->customer_id ?? null;

            $parcel->pick_type = 1;
            $parcel->pickup_date = Carbon::now()->format('Y-m-d H:i:s');
            $parcel->delivery_date = Carbon::now()->format('Y-m-d H:i:s');
            $parcel->discount         = $request->merchant_discount;
            $parcel->discount_amount  = $request->merchant_discount_amount;

            // End Pickup & Delivery Time
            $parcel->vat                    = $chargeDetails->vatTex;
            $parcel->vat_amount             = $chargeDetails->VatAmount;
            $parcel->delivery_charge        = $chargeDetails->deliveryChargeAmount;
            $parcel->total_delivery_amount      = $chargeDetails->totalDeliveryChargeAmount;
            $parcel->current_payable            = $chargeDetails->currentPayable;
            $parcel->note                       = $request->note;
            $parcel->rush_hour_amount           = $chargeDetails->rushHourServiceAmount ?? 0;
            $parcel->scheduled_amount           = $chargeDetails->scheduledServiceAmount ?? 0;
            $parcel->total_extra_cost           = $chargeDetails->totalExtraCost ?? 0;
            $parcel->packaging_id               = $request->packaging_id;
            $parcel->packaging_amount           = $chargeDetails->packagingAmount ?? 0;

            if (isset($chargeDetails->liquidFragileAmount)) {
                $parcel->liquid_fragile_amount      = $chargeDetails->liquidFragileAmount ?? 0;
            }

            $whoPays = (int) ($request->who_pays_either ?? 0);
            $paymentIntent = (string) ($request->payment_intent ?? 'pay_now');
            $paymentConfirmed = (bool) ($request->payment_confirmed ?? false);

            if ($paymentConfirmed) {
                $parcel->status = ParcelStatus::MARKETPLACE_PENDING;
                $parcel->payment_status = InvoiceStatus::PAID;
            } elseif ($whoPays === WhoPays::RECIPIENT) {
                $parcel->status = ParcelStatus::MARKETPLACE_RECEIVER_PAYMENT_AWAITING;
                $parcel->payment_status = InvoiceStatus::UNPAID;
            } elseif ($whoPays === WhoPays::SENDER && $paymentIntent === 'pay_later') {
                $parcel->status = ParcelStatus::MARKETPLACE_SENDER_PAYMENT_AWAITING;
                $parcel->payment_status = InvoiceStatus::UNPAID;
            } else {
                $parcel->status = ParcelStatus::MARKETPLACE_PENDING;
                $parcel->payment_status = InvoiceStatus::PROCESSING;
            }

            $parcel->save();
            $merchantIdForTracking                 = $parcel->merchant_id ?? 0;
            $trakingID                             = substr(strtotime(date('H:i:s')), 1) . 'C' . $merchantIdForTracking . $parcel->id;
            if (strlen($trakingID) <= 14) {
                $parcel->tracking_id               = Str::upper(settings()->par_track_prefix) . $trakingID;
            } else {
                $parcel->tracking_id               =  Str::upper(settings()->par_track_prefix) . substr(strtotime(date('H:i:s')), strlen($trakingID) - 14) . 'C' . $merchantIdForTracking . $parcel->id;
            }

            $parcel->delivery_charge_id         = $request->to_town_id;
            $parcel->who_pays_either            = $request->who_pays_either;
            $parcel->receiver_mpesa_phone       = $request->receiver_mpesa_phone ?? null;
            $parcel->special_discount                   = $request->special_discount;
            $parcel->departure_date_and_time            = $request->departure_date_and_time;
            $parcel->arrival_date_and_time_to_province  = $request->arrival_date_and_time_to_province;
            $parcel->policy                             = $request->policy == 'on' ? Status::ACTIVE : Status::INACTIVE;

            // cbm
            $cbm_details = [
                'package_type_id'               => $request->package_type_id,
                'length'                        => $request->length,
                'width'                         => $request->width,
                'height'                        => $request->height,
                'local_weight'                  => $request->local_weight,
                'quantity'                      => $request->quantity,
                'total_cbm'                     => $request->main_total_cbm,
                'total_weight'                  => $request->main_total_weight,
                'main_description'              => $request->main_description,
                'main_category_id'              => $request->main_category_id ?? null,
                'main_unit_parcel_service_cost' => $request->main_unit_parcel_service_cost ?? 0,
                'fragile_liquid_amount'         => $request->fragileLiquid ?? null,
                'rush_hour_service'             => $request->rush_hour_service ?? null,
                'extra_cost'                    => $request->extra_cost == 'on' ? '1' : null,
                'extra_cost_amount'             => $request->extra_cost_amount ?? 0,
                'extra_cost_description'        => $request->extra_cost_description ?? null,
                'packaging_id'                  => $request->packaging_id ?? null,
                'content_parcel'                => $request->content_parcel ?? null,
            ];

            $parcel->cbm_details        = $cbm_details;
            $parcel->total_weight       = $request->total_weight;
            $parcel->total_cubic_meters = $request->total_cbm;
            $parcel->total_valumetric_weight = $request->total_valumetric_weight;
            // total cbm

            $parcel->pickup_lat      = $request->pickup_lat;
            $parcel->pickup_long     = $request->pickup_long;
            $parcel->pickup_location = $request->pickup_location;
            $parcel->drop_latitude   = $request->drop_latitude;
            $parcel->drop_longitude  = $request->drop_longitude;
            $parcel->drop_location   = $request->drop_location;
            $parcel->distance_km     = $request->distance_km;


            $parcel->invoice_no             = settings()->order_invoice_prefix . '/' . $parcel->id;
            $parcel->save();

            $mpesaCheckoutId = trim((string) ($request->mpesa_checkout_request_id ?? ''));
            if ($mpesaCheckoutId !== '') {
                \App\Models\Backend\MpesaPayment::where('checkout_request_id', $mpesaCheckoutId)
                    ->whereNull('parcel_id')
                    ->update(['parcel_id' => $parcel->id]);
            }

            if ($whoPays === WhoPays::RECIPIENT && !empty($parcel->receiver_mpesa_phone)) {
                $paymentPayload = [
                    'merchant_id' => optional($parcel->merchant)->user_id,
                    'parcel_id' => $parcel->id,
                    'phone' => $parcel->receiver_mpesa_phone,
                    'amount' => $parcel->final_paid_amount ?: $parcel->total_delivery_amount,
                    'account_reference' => 'Receiver Payment',
                    'transaction_desc' => 'Receiver payment for parcel ' . $parcel->tracking_id,
                    'parcel_payload' => [
                        'parcel_id' => $parcel->id,
                        'who_pays' => 'receiver',
                    ],
                ];

                $result = app(MpesaStkPushService::class)->initiate($paymentPayload);
                if (empty($result['success'])) {
                    Log::warning('Receiver M-Pesa prompt failed (admin create)', [
                        'parcel_id' => $parcel->id,
                        'message' => $result['message'] ?? 'Unknown error',
                    ]);
                }
            }

            if ($parcel && $request->items) :
                foreach ($request->items as  $item) {
                    $this->parcelItems($parcel, $item);
                }
            endif;

            $receiverExist = Receiver::where(['name' => $parcel->customer_name, 'phone' => $parcel->customer_phone])->exists();
            if (!$receiverExist) :
                Receiver::create([
                    'name'   => $parcel->customer_name,
                    'phone'  => $parcel->customer_phone,
                    'address' => $parcel->customer_address
                ]);
            endif;

            // Parcel logs
            $log                         = new ParcelLogs;
            $log->merchant_id            = $request->merchant_id;
            $log->parcel_id              = $parcel->id;
            $log->pickup_address         = $request->pickup_address;
            $log->pickup_phone           = $request->pickup_phone;
            $log->customer_name          = $request->customer_name;
            $log->customer_phone         = $request->customer_phone;
            $log->customer_address       = $request->customer_address;

            $log->invoice_no             = $parcel->invoice_no;
            $log->cash_collection        = $request->cash_collection;
            if ($request->selling_price) {
                $log->selling_price          = $request->selling_price;
            }
            $log->total_delivery_amount  = $chargeDetails->totalDeliveryChargeAmount;
            $log->current_payable        = $chargeDetails->currentPayable;
            $log->note                   = $request->note;
            $log->save();
            if ($parcel) {
                if ($request->pick_type == 3) {
                    $parcel->booking_status = 1;
                    $parcel->save();
                }
            }

            try {
                if ($parcel->merchant) {
                    $msgNote = 'Dear ' . $parcel->merchant->business_name . ', Your parcel is successfully created. Your parcel with ID ' . $parcel->tracking_id;
                    if (isset($parcel->merchant->user->web_token)) {
                        app(PushNotificationService::class)->sendCreatePushNotificationToMessage($parcel->merchant->user->web_token, $msgNote);
                    }
                    if (isset($parcel->merchant->user->device_token)) {
                        app(PushNotificationService::class)->sendCreatePushNotificationToMessage($parcel->merchant->user->device_token, $msgNote);
                    }
                }
            } catch (\Exception $exception) {
            }

            DB::commit();
            if (SmsSendSettingHelper(SmsSendStatus::PARCEL_CREATE)) {
                $senderName = trim($parcel->sender_first_name . ' ' . $parcel->sender_last_name);
                $senderLabel = $parcel->merchant ? $parcel->merchant->business_name : $senderName;
                $msg = 'Dear ' . $parcel->customer_name . ', Your parcel is successfully created. Your parcel with ID ' . $parcel->tracking_id . ' parcel from ' . $senderLabel . ' (' . $parcel->cash_collection . ')';
                app(SmsService::class)->sendSms($parcel->customer_phone, $msg);
            }
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Admin parcel store failed.', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return false;
        }
    }

    public function duplicateStore($request)
    {
        try {
            DB::beginTransaction();
            $merchant                       = $request->merchant_id
                ? Merchant::with('user')->find($request->merchant_id)
                : null;
            $chargeDetails                  = json_decode($request->chargeDetails);
            $parcel                         = new Parcel();
            $parcel->merchant_id            = $request->merchant_id ?? null;

            $parcel->sender_company_name  = $request->company_name;
            $parcel->sender_first_name    = $request->first_name;
            $parcel->sender_last_name     = $request->last_name;
            $parcel->sender_email         = $request->sender_email;
            $parcel->sender_phone         = $request->pickup_phone;
            $parcel->sender_residential_address = $request->residential_address;

            $parcel->category_id            = $request->category_id;
            if ($request->weight !== "") {
                $parcel->weight                 = $request->weight;
            }

            if ($request->selling_price) {
                $parcel->selling_price          = $request->selling_price;
            }

            if ($request->parcel_value) {
                $parcel->parcel_value = $request->parcel_value;
            }

            if ($request->hasFile('parcel_file')) {
                $file = $request->file('parcel_file');
                $destinationPath = public_path('uploads/parcel');
                if (!File::exists($destinationPath)) {
                    File::makeDirectory($destinationPath, 0755, true);
                }
                $fileName = date('YmdHis') . '.' . $file->getClientOriginalExtension();
                $file->move($destinationPath, $fileName);
                $parcel->parcel_file = 'uploads/parcel/' . $fileName;
            }

            $parcel->from_state_id        = $request->from_state_id;
            $parcel->from_account_type    = 1;
            $parcel->from_point_type      = $request->from_point_type ?? 1;

            if ($request->from_point_type == 1) {
                $parcel->from_town            = $request->from_town;
                $parcel->from_building        = $request->from_building;
                $parcel->from_city_id         = $request->from_city_id;
                $parcel->from_portal_code     = $request->from_portal_code;
            } elseif ($request->from_point_type == 2) {
                $parcel->from_town            = null;
                $parcel->from_building        = null;
                $parcel->from_city_id         = null;
                $parcel->from_portal_code     = null;
            }


            $parcel->to_state_id          = $request->to_state_id;
            $parcel->to_account_type      = 1;
            $parcel->to_point_type        = $request->to_point_type ?? 1;
            if ($request->to_point_type == 1) {
                $parcel->to_town              = $request->to_town;
                $parcel->to_building          = $request->to_building;
                $parcel->to_city_id           = $request->to_city_id;
                $parcel->to_portal_code       = $request->to_portal_code;
            } elseif ($request->to_point_type == 2) {
                $parcel->to_town              = null;
                $parcel->to_building          = null;
                $parcel->to_city_id           = null;
                $parcel->to_portal_code       = null;
            }

            if ($request->from_whatsapp_number) :
                $parcel->from_whatsapp_number = $request->from_whatsapp_number;
            elseif ($request->hub_from_whatsapp_number) :
                $parcel->from_whatsapp_number = $request->hub_from_whatsapp_number;
            endif;
            $parcel->to_whatsapp_number   = $request->to_whatsapp_number;
            $parcel->receiver_whatsapp_number   = $request->receiver_whatsapp_number;

            $parcel->merchant_shop_id       = null;
            $parcel->pickup_phone           = $request->pickup_phone;
            $parcel->pickup_address         = $request->pickup_address;

            $parcel->to_merchant_id         = null;
            $parcel->customer_company_name  = $request->customer_company_name;
            $parcel->customer_id            = null;
            $parcel->customer_first_name    = $request->customer_first_name;
            $parcel->customer_last_name     = $request->customer_last_name;
            $parcel->customer_name          = $request->customer_name;
            $parcel->receiver_email         = $request->receiver_email;
            $parcel->customer_phone         = $request->customer_phone;
            $parcel->customer_address       = $request->customer_address;

            $parcel->to_merchant_id = null;
            $parcel->customer_id = null;

            $parcel->pick_type        = $request->pick_type;
            if ($request->pick_type == 3) :
                $parcel->pickup_date      = Carbon::parse($request->pickup_date)->format('Y-m-d H:i:s');
                $parcel->delivery_date    = Carbon::parse($request->delivery_date)->format('Y-m-d H:i:s');
            elseif ($request->pick_type == 2) :
                $parcel->pickup_date      = Carbon::now()->addDay(1)->format('Y-m-d H:i:s');
                $parcel->delivery_date    = Carbon::now()->addDay(1)->format('Y-m-d H:i:s');
            else :
                $parcel->pickup_date      = Carbon::now()->format('Y-m-d H:i:s');
                $parcel->delivery_date    = Carbon::now()->format('Y-m-d H:i:s');
            endif;

            $parcel->discount         = $request->merchant_discount;
            $parcel->discount_amount  = $request->merchant_discount_amount;

            // End Pickup & Delivery Time
            $parcel->vat                    = $chargeDetails->vatTex;
            $parcel->vat_amount             = $chargeDetails->VatAmount;
            $parcel->delivery_charge        = $chargeDetails->deliveryChargeAmount;
            $parcel->total_delivery_amount  = $chargeDetails->totalDeliveryChargeAmount;
            $parcel->current_payable        = $chargeDetails->currentPayable;
            $parcel->note                   = $request->note;
            $parcel->status                 = ParcelStatus::PENDING;
            $parcel->rush_hour_amount       = $chargeDetails->rushHourServiceAmount;
            $parcel->scheduled_amount       = $chargeDetails->scheduledServiceAmount;
            $parcel->total_extra_cost       = $chargeDetails->totalExtraCost;
            $parcel->packaging_id           = $request->packaging_id;
            $parcel->packaging_amount       = $chargeDetails->packagingAmount;
            if (isset($chargeDetails->liquidFragileAmount)) {
                $parcel->liquid_fragile_amount      = $chargeDetails->liquidFragileAmount ?? 0;
            }
            $parcel->save();
            $trakingID                             = substr(strtotime(date('H:i:s')), 1) . 'C' . $parcel->merchant_id . $parcel->id;
            if (strlen($trakingID) <= 14) {
                $parcel->tracking_id               = Str::upper(settings()->par_track_prefix) . $trakingID;
            } else {
                $parcel->tracking_id               =  Str::upper(settings()->par_track_prefix) . substr(strtotime(date('H:i:s')), strlen($trakingID) - 14) . 'C' . $parcel->merchant_id . $parcel->id;
            }

            $parcel->delivery_charge_id         = $request->to_town_id;

            $parcel->who_pays_either            = $request->who_pays_either;
            $parcel->special_discount = $request->special_discount;
            $parcel->departure_date_and_time            = $request->departure_date_and_time;
            $parcel->arrival_date_and_time_to_province  = $request->arrival_date_and_time_to_province;
            $parcel->policy                             = $request->policy == 'on' ? Status::ACTIVE : Status::INACTIVE;

            // cbm
            $cbm_details = [
                'package_type_id'               => $request->package_type_id,
                'length'                        => $request->length,
                'width'                         => $request->width,
                'height'                        => $request->height,
                'local_weight'                  => $request->local_weight,
                'quantity'                      => $request->quantity,
                'total_cbm'                     => $request->main_total_cbm,
                'total_weight'                  => $request->main_total_weight,
                'main_description'              => $request->main_description,
                'main_category_id'              => $request->main_category_id ?? null,
                'main_unit_parcel_service_cost' => $request->main_unit_parcel_service_cost ?? 0,
                'fragile_liquid_amount'         => $request->fragileLiquid ?? null,
                'rush_hour_service'             => $request->rush_hour_service ?? null,
                'extra_cost'                    => $request->extra_cost == 'on' ? '1' : null,
                'extra_cost_amount'             => $request->extra_cost_amount ?? 0,
                'extra_cost_description'        => $request->extra_cost_description ?? null,
                'packaging_id'                  => $request->packaging_id ?? null,
                'content_parcel'                => $request->content_parcel ?? null,
            ];

            $parcel->cbm_details        = $cbm_details;
            $parcel->total_weight       = $request->total_weight;
            $parcel->total_cubic_meters = $request->total_cbm;
            $parcel->total_valumetric_weight = $request->total_valumetric_weight;
            // total cbm

            $parcel->pickup_lat      = $request->pickup_lat;
            $parcel->pickup_long     = $request->pickup_long;
            $parcel->pickup_location = $request->pickup_location;
            $parcel->drop_latitude   = $request->drop_latitude;
            $parcel->drop_longitude  = $request->drop_longitude;
            $parcel->drop_location   = $request->drop_location;
            $parcel->distance_km     = $request->distance_km;

            $parcel->save();
            $parcel->invoice_no             = settings()->order_invoice_prefix . '/' . $parcel->id;
            $parcel->save();

            if ($parcel && $request->items) :
                foreach ($request->items as  $item) {
                    $this->parcelItems($parcel, $item);
                }
            endif;

            $receiverExist = Receiver::where(['name' => $parcel->customer_name, 'phone' => $parcel->customer_phone])->exists();
            if (!$receiverExist) :
                Receiver::create([
                    'name'   => $parcel->customer_name,
                    'phone'  => $parcel->customer_phone,
                    'address' => $parcel->customer_address
                ]);
            endif;

            // Parcel logs
            $log                         = new ParcelLogs;
            $log->merchant_id            = $request->merchant_id;
            $log->parcel_id              = $parcel->id;
            $log->pickup_address         = $request->pickup_address;
            $log->pickup_phone           = $request->pickup_phone;
            $log->customer_name          = $request->customer_name;
            $log->customer_phone         = $request->customer_phone;
            $log->customer_address       = $request->customer_address;

            $log->invoice_no             = $parcel->invoice_no;
            $log->cash_collection        = $request->cash_collection;
            if ($request->selling_price) {
                $log->selling_price          = $request->selling_price;
            }
            $log->total_delivery_amount  = $chargeDetails->totalDeliveryChargeAmount;
            $log->current_payable        = $chargeDetails->currentPayable;
            $log->note                   = $request->note;
            $log->save();
            if ($parcel) {
                if ($request->pick_type == 3) {
                    $parcel->booking_status = 1;
                    $parcel->save();
                }
            }
            DB::commit();

            try {
                if ($parcel->merchant) {
                    $msgNote = 'Dear ' . $parcel->merchant->business_name . ', Your parcel is successfully duplicate created. Your parcel with ID ' . $parcel->tracking_id;
                    if (isset($parcel->merchant->user->web_token)) {
                        app(PushNotificationService::class)->sendCreatePushNotificationToMessage($parcel->merchant->user->web_token, $msgNote);
                    }
                    if (isset($parcel->merchant->user->device_token)) {
                        app(PushNotificationService::class)->sendCreatePushNotificationToMessage($parcel->merchant->user->device_token, $msgNote);
                    }
                }
            } catch (\Exception $exception) {
                // dd($exception)
            }

            if (SmsSendSettingHelper(SmsSendStatus::PARCEL_CREATE)) {
                $senderName = trim($parcel->sender_first_name . ' ' . $parcel->sender_last_name);
                $senderLabel = $parcel->merchant ? $parcel->merchant->business_name : $senderName;
                $msg = 'Dear ' . $parcel->customer_name . ', Your parcel is successfully duplicate created. Your parcel with ID ' . $parcel->tracking_id . ' parcel from ' . $senderLabel . ' (' . $parcel->cash_collection . ')';
                app(SmsService::class)->sendSms($parcel->customer_phone, $msg);
            }
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    public function update($id, $request)
    {
        try {
            DB::beginTransaction();
            $merchant                       = Merchant::with('user')->find($request->merchant_id);
            $chargeDetails = json_decode($request->chargeDetails);

            $parcel                         = Parcel::find($id);
            $parcel->merchant_id            = $request->merchant_id;

            $parcel->sender_company_name  = $request->company_name;
            $parcel->sender_first_name    = $request->first_name;
            $parcel->sender_last_name     = $request->last_name;
            $parcel->sender_email         = $request->sender_email;
            $parcel->sender_phone         = $request->pickup_phone;
            $parcel->sender_residential_address = $request->residential_address;

            $parcel->category_id            = $request->category_id;
            if ($request->weight !== "") {
                $parcel->weight                 = $request->weight;
            }
            // $parcel->invoice_no             = $request->invoice_no;
            if ($request->selling_price) {
                $parcel->selling_price          = $request->selling_price;
            }

            $parcel->from_state_id        = $request->from_state_id;
            $parcel->from_account_type    = 1;
            $parcel->from_point_type      = $request->from_point_type;

            if ($request->from_point_type == 1) {
                $parcel->from_town            = $request->from_town;
                $parcel->from_building        = $request->from_building;
                $parcel->from_city_id         = $request->from_city_id;
                $parcel->from_portal_code     = $request->from_portal_code;
            } elseif ($request->from_point_type == 2) {
                $parcel->from_town            = null;
                $parcel->from_building        = null;
                $parcel->from_city_id         = null;
                $parcel->from_portal_code     = null;
            }


            $parcel->to_state_id          = $request->to_state_id;
            $parcel->to_account_type      = 1;
            $parcel->to_point_type        = $request->to_point_type;
            $parcel->status               = ParcelStatus::MARKETPLACE_PENDING;
            if ($request->to_point_type == 1) {
                $parcel->to_town              = $request->to_town;
                $parcel->to_building          = $request->to_building;
                $parcel->to_city_id           = $request->to_city_id;
                $parcel->to_portal_code       = $request->to_portal_code;
            } elseif ($request->to_point_type == 2) {
                $parcel->to_town              = null;
                $parcel->to_building          = null;
                $parcel->to_city_id           = null;
                $parcel->to_portal_code       = null;
            }

            if ($request->from_whatsapp_number) :
                $parcel->from_whatsapp_number = $request->from_whatsapp_number;
            elseif ($request->hub_from_whatsapp_number) :
                $parcel->from_whatsapp_number = $request->hub_from_whatsapp_number;
            endif;
            $parcel->to_whatsapp_number   = $request->to_whatsapp_number;
            if ($request->receiver_whatsapp_number) {
                $parcel->receiver_whatsapp_number   = $request->receiver_whatsapp_number;
            }

            $parcel->merchant_shop_id       = $request->shop_id;
            $parcel->pickup_phone           = $request->pickup_phone;
            $parcel->pickup_address         = $request->pickup_address;

            $parcel->to_merchant_id         = $request->to_merchant_id;
            $parcel->customer_company_name  = $request->customer_company_name;
            $parcel->customer_id            = $request->customer_id;
            $parcel->customer_first_name    = $request->customer_first_name;
            $parcel->customer_last_name     = $request->customer_last_name;
            $parcel->customer_name          = $request->customer_name;
            $parcel->receiver_email         = $request->receiver_email;
            $parcel->customer_phone         = $request->customer_phone;
            $parcel->customer_address       = $request->customer_address;

            $parcel->pick_type        = $request->pick_type;
            if ($request->pick_type == 3) :
                $parcel->pickup_date      = Carbon::parse($request->pickup_date)->format('Y-m-d H:i:s');
                $parcel->delivery_date    = Carbon::parse($request->delivery_date)->format('Y-m-d H:i:s');
            elseif ($request->pick_type == 2) :
                $parcel->pickup_date      = Carbon::now()->addDay(1)->format('Y-m-d H:i:s');
                $parcel->delivery_date    = Carbon::now()->addDay(1)->format('Y-m-d H:i:s');
            else :
                $parcel->pickup_date      = Carbon::now()->format('Y-m-d H:i:s');
                $parcel->delivery_date    = Carbon::now()->format('Y-m-d H:i:s');
            endif;

            $parcel->discount         = $request->merchant_discount;
            $parcel->discount_amount  = $request->merchant_discount_amount;

            // End Pickup & Delivery Time
            $parcel->note                       = $request->note;
            if ($chargeDetails) {
                $parcel->vat                    = $chargeDetails->vatTex;
                $parcel->vat_amount             = $chargeDetails->VatAmount;
                $parcel->delivery_charge        = $chargeDetails->deliveryChargeAmount;
                $parcel->total_delivery_amount  = $chargeDetails->totalDeliveryChargeAmount;
                $parcel->current_payable        = $chargeDetails->currentPayable;
                $parcel->rush_hour_amount       = $chargeDetails->rushHourServiceAmount;
                $parcel->scheduled_amount       = $chargeDetails->scheduledServiceAmount;
                $parcel->total_extra_cost       = $chargeDetails->totalExtraCost;
                $parcel->packaging_id           = $request->packaging_id;
                $parcel->packaging_amount       = $chargeDetails->packagingAmount;
                if (isset($chargeDetails->liquidFragileAmount)) {
                    $parcel->liquid_fragile_amount      = $chargeDetails->liquidFragileAmount;
                } else {
                    $parcel->liquid_fragile_amount      = null;
                }
            }

            $parcel->delivery_charge_id = $request->to_town_id;
            $parcel->who_pays_either            = $request->who_pays_either;
            $parcel->special_discount = $request->special_discount;
            $parcel->departure_date_and_time            = $request->departure_date_and_time;
            $parcel->arrival_date_and_time_to_province  = $request->arrival_date_and_time_to_province;
            $parcel->policy                             = $request->policy == 'on' ? Status::ACTIVE : Status::INACTIVE;

            // cbm
            $cbm_details = [
                'package_type_id'               => $request->package_type_id,
                'length'                        => $request->length,
                'width'                         => $request->width,
                'height'                        => $request->height,
                'local_weight'                  => $request->local_weight,
                'quantity'                      => $request->quantity,
                'total_cbm'                     => $request->main_total_cbm,
                'total_weight'                  => $request->main_total_weight,
                'main_description'              => $request->main_description,
                'main_category_id'              => $request->main_category_id ?? null,
                'main_unit_parcel_service_cost' => $request->main_unit_parcel_service_cost ?? 0,
                'fragile_liquid_amount'         => $request->fragileLiquid ?? null,
                'rush_hour_service'             => $request->rush_hour_service ?? null,
                'extra_cost'                    => $request->extra_cost == 'on' ? '1' : null,
                'extra_cost_amount'             => $request->extra_cost_amount ?? 0,
                'extra_cost_description'        => $request->extra_cost_description ?? null,
                'packaging_id'                  => $request->packaging_id ?? null,
                'content_parcel'                => $request->content_parcel ?? null,
            ];

            $parcel->cbm_details        = $cbm_details;
            $parcel->total_weight       = $request->total_weight;
            $parcel->total_cubic_meters = $request->total_cbm;
            $parcel->total_valumetric_weight = $request->total_valumetric_weight;
            //total cbm

            $parcel->pickup_lat      = $request->pickup_lat;
            $parcel->pickup_long     = $request->pickup_long;
            $parcel->pickup_location = $request->pickup_location;
            $parcel->drop_latitude   = $request->drop_latitude;
            $parcel->drop_longitude  = $request->drop_longitude;
            $parcel->drop_location   = $request->drop_location;
            $parcel->distance_km     = $request->distance_km;

            $parcel->save();

            if ($parcel) :
                ParcelItem::where('parcel_id', $parcel->id)->delete();
                if ($request->items) {
                    foreach ($request->items as  $item) {
                        $this->parcelItems($parcel, $item);
                    }
                }
            endif;

            $receiverExist = Receiver::where(['name' => $parcel->customer_name, 'phone' => $parcel->customer_phone])->exists();
            if (!$receiverExist) :
                Receiver::create([
                    'name'   => $parcel->customer_name,
                    'phone'  => $parcel->customer_phone,
                    'address' => $parcel->customer_address
                ]);
            endif;

            if ($parcel) {
                if ($request->pick_type == 3) {
                    $parcel->booking_status = 1;
                    $parcel->save();
                }
            }

            try {
                $msgNote = 'Dear ' . $parcel->merchant->business_name . ', Your parcel is successfully updated. Your parcel with ID ' . $parcel->tracking_id;
                if (isset($parcel->merchant->user->web_token)) {
                    app(PushNotificationService::class)->sendCreatePushNotificationToMessage($parcel->merchant->user->web_token, $msgNote);
                }
                if (isset($parcel->merchant->user->device_token)) {
                    app(PushNotificationService::class)->sendCreatePushNotificationToMessage($parcel->merchant->user->device_token, $msgNote);
                }
            } catch (\Exception $exception) {
                // dd($exception);
            }
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    public function storeApi($request)
    {
        try {
            DB::beginTransaction();
            $merchant_id = Auth::user()->merchant->id;
            $merchant = Merchant::with('user')->find($merchant_id);

            // Item data calculation
            $total_cubic_meter = 0;
            $total_weight = 0;
            $total_valumetric_weight = 0;
            $liquidFragileAmount = 0;
            foreach ($request->items as  $itemData) {
                $cubic_centimeter = ($itemData['length'] * $itemData['width'] * $itemData['height']);
                $cubic_meter = ($cubic_centimeter / 1000000); //cubic meter
                $total_valumetric_weight += ($cubic_centimeter / 5000); //valumetric weight
                $cubic_meter = ($cubic_meter * $itemData['quantity']);
                $total_cubic_meter += number_format($cubic_meter, 3);

                $total_single_weight = ($itemData['local_weight'] * $itemData['quantity']);
                $total_weight += $total_single_weight;

                // Liquid/Fragile
                if ($itemData['fragile_liquid'] == 1) {
                    $total_distance_km = $request->distance_km;
                    $inside_distance_km = settings()->inside_city_distance;
                    if ($total_distance_km > $inside_distance_km) {
                        $liquidFragileAmount += SettingHelper('fragile_liquid_outside_charge');
                    } else {
                        $liquidFragileAmount += SettingHelper('fragile_liquid_charge');
                    }
                }
            }

            // delivery Charge
            $total_cost = DeliveryChargeHelper::instance()->deliveryCharge($request);

            //cod charge calculation
            $codChargeAmount = $request->selling_price * (0 / 100);

            // Packaging
            if ($request->packaging_id) {
                $packaging = Packaging::find($request->packaging_id);
                $packagingAmount = $packaging->price;
            } else {
                $packagingAmount = 0;
            }

            // totalAmount
            $totalAmount = ($codChargeAmount + $total_cost + $liquidFragileAmount + $packagingAmount);

            // Vat
            $vat = $this->percentage($totalAmount, $merchant->vat);
            $totalAmount += $vat;

            // Merchant discount
            $discountAmount         = (($totalAmount / 100) * $merchant->discount);
            $totalAmount -= $discountAmount;

            // Current Payable
            $totalCurrentAmount = ($request->selling_price - $totalAmount);

            // Charge Details parent table
            $chargeDetails = [
                'vatTex'  => $merchant->vat,
                'totalCashCollection' => $request->selling_price,
                'deliveryChargeAmount' => $total_cost,
                'codChargeAmount' => $codChargeAmount,
                'VatAmount' => $vat,
                'liquidFragileAmount' => $liquidFragileAmount,
                'packagingAmount' => $packagingAmount,
                'totalDeliveryChargeAmount' => $totalAmount,
                'currentPayable' => $totalCurrentAmount,
            ];

            // Create Parcel
            $parcel                         = new Parcel();
            $parcel->merchant_id            = $merchant_id;
            $parcel->category_id            = $request->category_id;
            $parcel->weight                 = $total_weight;
            $parcel->from_state_id        = $request->from_province;
            $parcel->from_city_id         = $request->from_city;
            $parcel->from_portal_code     = $request->from_portal_code;
            $parcel->from_whatsapp_number = $request->from_whatsapp_number;
            $parcel->to_whatsapp_number   = $request->to_whatsapp_number;
            if ($request->receiver_whatsapp_number) {
                $parcel->receiver_whatsapp_number   = $request->receiver_whatsapp_number;
            }
            $parcel->to_state_id          = $request->to_province;
            $parcel->to_city_id           = $request->to_city;
            $parcel->to_portal_code       = $request->to_portal_code;
            $parcel->status               = ParcelStatus::MARKETPLACE_PENDING;
            $parcel->merchant_shop_id       = $request->shop_id;
            $parcel->pickup_phone           = $request->pickup_phone;
            $parcel->pickup_address         = $request->pickup_address;
            $parcel->customer_name          = $request->receiver_name;
            $parcel->receiver_email           = $request->receiver_email;
            $parcel->customer_phone         = $request->receiver_phone;
            $parcel->customer_address       = $request->customer_address;
            if ($request->pickup_type  == 3) {
                $parcel->pickup_date      = Carbon::parse($request->schedule_date)->format('Y-m-d');
                $parcel->delivery_date    = Carbon::parse($request->schedule_date)->format('Y-m-d');
            } elseif ($request->pickup_type  == 2) {
                $parcel->pickup_date      = Carbon::now()->addDay(1)->format('Y-m-d');
                $parcel->delivery_date    = Carbon::now()->addDay(1)->format('Y-m-d');
            } else {
                $parcel->pickup_date      = Carbon::now()->format('Y-m-d');
                $parcel->delivery_date    = Carbon::now()->format('Y-m-d');
            }
            $parcel->pick_type        = $request->pickup_type;
            $parcel->discount         = $merchant->discount;
            $parcel->discount_amount  = $discountAmount;

            // End Pickup & Delivery Time
            $parcel->vat                    = $chargeDetails['vatTex'];
            $parcel->vat_amount             = $chargeDetails['VatAmount'];
            $parcel->delivery_charge        = $chargeDetails['deliveryChargeAmount'];

            // Charge info
            $parcel->total_delivery_amount  = $chargeDetails['totalDeliveryChargeAmount'];
            $parcel->current_payable        = $chargeDetails['currentPayable'];
            $parcel->note                   = $request->note;
            $parcel->save();

            if ($request->packaging_id) {
                $parcel->packaging_id           = $request->packaging_id;
                $parcel->packaging_amount       = $chargeDetails['packagingAmount'];
            }

            if (isset($chargeDetails['liquidFragileAmount'])) {
                $parcel->liquid_fragile_amount      = $chargeDetails['liquidFragileAmount'] ?? 0;
            }

            $trackingID                             = substr(strtotime(date('H:i:s')), 1) . 'C' . $parcel->merchant_id . $parcel->id;
            if (strlen($trackingID) <= 14) {
                $parcel->tracking_id               = Str::upper(settings()->par_track_prefix) . $trackingID;
            } else {
                $parcel->tracking_id               =  Str::upper(settings()->par_track_prefix) . substr(strtotime(date('H:i:s')), strlen($trackingID) - 14) . 'C' . $parcel->merchant_id . $parcel->id;
            }

            $parcel->delivery_charge_id   = $request->to_town_id;
            $parcel->who_pays_either            = $request->who_pays_either;
            $parcel->special_discount = $request->special_discount ?? 0;

            // cbm
            if (count($request->items) > 0) {
                // total_cbm
                $total_cbm = ($request->items[1]['length'] * $request->items[1]['width'] * $request->items[1]['height']);
                $cbm_kg = ($total_cbm / 1000000); //cbm kg
                $total_cbm_value = ($cbm_kg * $request->items[1]['quantity']);
                // Liquid/Fragile
                if ($itemData['fragile_liquid'] == 1) {
                    $total_distance_km = $request->distance_km;
                    $inside_distance_km = settings()->inside_city_distance;
                    if ($total_distance_km > $inside_distance_km) {
                        $liquidFragileAmount = SettingHelper('fragile_liquid_outside_charge');
                    } else {
                        $liquidFragileAmount = SettingHelper('fragile_liquid_charge');
                    }
                } else {
                    $liquidFragileAmount = null;
                }

                $cbm_details = [
                    'length'       => $request->items[1]['length'],
                    'width'        => $request->items[1]['width'],
                    'height'       => $request->items[1]['height'],
                    'local_weight' => $request->items[1]['local_weight'],
                    'quantity'     => $request->items[1]['quantity'],
                    'total_cbm'    => number_format($total_cbm_value, 3),
                    'total_weight' => ($request->items[1]['local_weight'] * $request->items[1]['quantity']),
                    'main_category_id' => $request->items[1]['item_category_id'],
                    'main_description' => $request->items[1]['item_description'],
                    'main_item_value' => $request->items[1]['item_value'],
                    'main_unit_parcel_service_cost' => $request->items[1]['item_unit_parcel_service_cost'],
                    'fragile_liquid_amount' => $liquidFragileAmount,
                ];
                $firstItemDitemension     =  ($request->items[1]['length'] * $request->items[1]['width'] * $request->items[1]['height']);
                $total_valumetric_weight += $firstItemDitemension / 5000;
            }


            $parcel->cbm_details        = $cbm_details;
            $parcel->total_weight       = $total_weight;
            $parcel->total_cubic_meters = $total_cubic_meter;
            $parcel->total_valumetric_weight = $total_valumetric_weight;
            // End cbm

            $parcel->pickup_lat      = $request->pickup_lat;
            $parcel->pickup_long     = $request->pickup_long;
            $parcel->pickup_location = $request->pickup_location;
            $parcel->drop_latitude   = $request->drop_latitude;
            $parcel->drop_longitude  = $request->drop_longitude;
            $parcel->drop_location   = $request->drop_location;
            $parcel->distance_km     = $request->distance_km;

            $parcel->invoice_no             = settings()->order_invoice_prefix . '/' . $parcel->id;
            $parcel->save();
            if ($parcel && $request->items) {
                if (count($request->items) > 0) {
                    foreach ($request->items as $kay => $item) {
                        if ($kay == 1) {
                            continue;
                        }
                        // array to object
                        $itemData = (object)$item;

                        // total_cbm
                        $total_cbm = ($itemData->length * $itemData->width * $itemData->height);
                        $cbm_kg = ($total_cbm / 1000000); //cbm kg
                        $total_cbm_value = ($cbm_kg * $itemData->quantity);

                        // Liquid/Fragile
                        if ($itemData->fragile_liquid == 1) {
                            $total_distance_km = $request->distance_km;
                            $inside_distance_km = settings()->inside_city_distance;
                            if ($total_distance_km > $inside_distance_km) {
                                $liquidFragileAmount = SettingHelper('fragile_liquid_outside_charge');
                            } else {
                                $liquidFragileAmount = SettingHelper('fragile_liquid_charge');
                            }
                        } else {
                            $liquidFragileAmount = null;
                        }

                        //Create Item
                        ParcelItem::create([
                            'parcel_id'      => $parcel->id,
                            'length'         => $itemData->length,
                            'width'          => $itemData->width,
                            'height'         => $itemData->height,
                            'weight'         => $itemData->local_weight,
                            'quantity'       => $itemData->quantity,
                            'total_weight'   => ($itemData->local_weight * $itemData->quantity),
                            'total_cbm'      => number_format($total_cbm_value, 3),
                            'category_id'    => $itemData->item_category_id,
                            'description'  => $itemData->item_description,
                            'unit_parcel_service_cost' => $itemData->item_unit_parcel_service_cost,
                            'fragile_liquid_amount' => $liquidFragileAmount,
                        ]);
                    }
                }
            }

            $receiverExist = Receiver::where(['name' => $parcel->receiver_name, 'phone' => $parcel->receiver_phone])->exists();
            if (!$receiverExist) {
                Receiver::create([
                    'name'   => $parcel->receiver_name,
                    'phone'  => $parcel->receiver_phone,
                    'address' => $parcel->receiver_address
                ]);
            }

            // Parcel logs
            $log                         = new ParcelLogs;
            $log->merchant_id            = $merchant_id;
            $log->parcel_id              = $parcel->id;
            $log->pickup_address         = $request->pickup_address;
            $log->pickup_phone           = $request->pickup_phone;
            $log->customer_name          = $request->receiver_name;
            $log->customer_phone         = $request->receiver_phone;
            $log->customer_address       = $request->receiver_address;

            $log->invoice_no             = $parcel->invoice_no;
            $log->cash_collection        = $request->cash_collection;
            if ($request->selling_price) {
                $log->selling_price          = $request->selling_price;
            }
            $log->total_delivery_amount  = $chargeDetails['totalDeliveryChargeAmount'];
            $log->current_payable        = $chargeDetails['currentPayable'];
            $log->note                   = $request->note;
            $log->save();

            // Push Notification website and App
            try {
                $msgNote = 'Dear ' . $parcel->merchant->business_name . ', Your parcel is successfully created. Your parcel with ID ' . $parcel->tracking_id;
                if (isset($parcel->merchant->user->web_token)) {
                    app(PushNotificationService::class)->sendCreatePushNotificationToMessage($parcel->merchant->user->web_token, $msgNote);
                }
                if (isset($parcel->merchant->user->device_token)) {
                    app(PushNotificationService::class)->sendCreatePushNotificationToMessage($parcel->merchant->user->device_token, $msgNote);
                }
            } catch (\Exception $exception) {
                // dd($exception);
            }
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    public function calculateTheQuote($request)
    {
        try {
            // Merchant
            $merchant_id = Auth::user()->merchant->id;
            $merchant                       = Merchant::with('user')->find($merchant_id);

            // Item data calculation
            $total_cubic_meter = 0;
            $total_weight = 0;
            $liquidFragileAmount = 0;
            foreach ($request->items as  $itemData) {
                $cubic_centimeter = ($itemData['length'] * $itemData['width'] * $itemData['height']);
                $cubic_meter = ($cubic_centimeter / 1000000); //cubic meter
                $cubic_meter = ($cubic_meter * $itemData['quantity']);
                $total_cubic_meter += number_format($cubic_meter, 3);

                $total_single_weight = ($itemData['local_weight'] * $itemData['quantity']);
                $total_weight += $total_single_weight;

                // liquidFragileAmount
                if ($itemData['fragile_liquid'] == 1) {
                    $total_distance_km = $request->distance_km;
                    $inside_distance_km = settings()->inside_city_distance;
                    if ($total_distance_km > $inside_distance_km) {
                        $liquidFragileAmount += SettingHelper('fragile_liquid_outside_charge');
                    } else {
                        $liquidFragileAmount += SettingHelper('fragile_liquid_charge');
                    }
                }
            }

            // delivery Charge
            $total_cost  = DeliveryChargeHelper::instance()->deliveryCharge($request);

            //cod charge calculation
            $codChargeAmount = $request->selling_price * (0 / 100);

            // packaging
            if ($request->packaging_id) {
                $packaging = Packaging::find($request->packaging_id);
                $packagingAmount = $packaging->price;
            } else {
                $packagingAmount = 0;
            }
            // totalAmount
            $totalAmount = ($codChargeAmount + $total_cost + $liquidFragileAmount + $packagingAmount);

            // Vat
            $vat = $this->percentage($totalAmount, $merchant->vat);
            $totalAmount += $vat;

            // merchant_discount_amount
            $discountAmount         = (($totalAmount / 100) * $merchant->discount);
            $totalAmount -= $discountAmount;

            // currentPayable
            $totalCurrentAmount = ($request->selling_price - $totalAmount);

            // charge Details parent table
            $chargeDetails = [
                'vatTex'  => $merchant->vat,
                'totalCashCollection' => number_format($request->selling_price, 2),
                'deliveryChargeAmount' => number_format($total_cost, 2),
                'codChargeAmount' => number_format($codChargeAmount, 2),
                'VatAmount' => number_format($vat, 2),
                'liquidFragileAmount' => number_format($liquidFragileAmount, 2),
                'packagingAmount' => number_format($packagingAmount, 2),
                'totalDeliveryChargeAmount' => number_format($totalAmount, 2),
                'currentPayable' => number_format($totalCurrentAmount, 2),
            ];
            return $chargeDetails;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function parcelItemCalculate($request)
    {
        try {
            $liquidFragileAmount = 0;
            $total_weight = 0;
            $data = array();
            foreach ($request->items as  $itemData) {
                $$nestedData = [];
                // length
                $nestedData['length'] = $itemData['length'];
                // width
                $nestedData['width'] = $itemData['width'];
                // height
                $nestedData['height'] = $itemData['height'];
                // height
                $nestedData['local_weight'] = $itemData['local_weight'];
                // quantity
                $nestedData['quantity'] = $itemData['quantity'];
                // quantity
                $nestedData['total_weight'] = $itemData['local_weight'] * $itemData['quantity'];
                $total_weight += $nestedData['total_weight'];
                // total_cbm
                $total_cbm = ($itemData['length'] * $itemData['width'] * $itemData['height']);
                $cbm_kg = ($total_cbm / 1000000); //cbm kg
                $total_cbm_value = ($cbm_kg * $itemData['quantity']);
                $nestedData['total_cbm'] = number_format($total_cbm_value, 3);
                // category
                $nestedData['item_category_id'] = $itemData['item_category_id'];
                // description
                $nestedData['item_description'] = $itemData['item_description'];
                // item value
                $nestedData['item_value'] = $itemData['item_value'];
                // Unit parcel service cost
                $total_cost  = DeliveryChargeHelper::instance()->deliveryCharge($request);
                $nestedData['item_unit_parcel_service_cost'] = $total_cost;
                // fragile liquid
                if ($itemData['fragile_liquid'] == 1) {
                    $total_distance_km = $request->distance_km;
                    $inside_distance_km = settings()->inside_city_distance;
                    if ($total_distance_km > $inside_distance_km) {
                        $liquidFragileAmount += SettingHelper('fragile_liquid_outside_charge');
                    } else {
                        $liquidFragileAmount += SettingHelper('fragile_liquid_charge');
                    }
                }
                $nestedData['fragile_liquid'] = $itemData['fragile_liquid'];
                $nestedData['fragile_liquid_amount'] = $liquidFragileAmount;
                $data[] = $nestedData;
            }
            $mainData['items'] = $data;
            $mainData['number_of_parcel'] = count($request->items);
            $mainData['total_weight'] = $total_weight;
            return ($mainData);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function delete($id)
    {
        return Parcel::destroy($id);
    }

    public function generateUniqueID()
    {
        do {
            $merchant_unique_id = random_int(100000, 999999);
        } while (Merchant::where("merchant_unique_id", "=", $merchant_unique_id)->first());
        return $merchant_unique_id;
    }

    public function parcelItems($parcel, $item)
    {
        try {
            $item = (object)$item;
            ParcelItem::create([
                'parcel_id'                         => $parcel->id,
                'package_type_id'                   => $item->package_type_id,
                'length'                            => $item->length,
                'width'                             => $item->width,
                'height'                            => $item->height,
                'weight'                            => $item->local_weight,
                'quantity'                          => $item->quantity,
                'category_id'                       => $item->category_id,
                'fragile_liquid_amount'             => $item->fragile_liquid ?? null,
                'rush_hour_service'                 => $item->rush_hour_service ?? null,
                'extra_cost'                        => $item->extra_cost ?? null,
                'extra_cost_amount'                 => $item->extra_cost_amount ?? 0,
                'extra_cost_description'            => $item->extra_cost_description ?? null,
                'packaging_id'                      => $item->packaging_id ?? null,
                'description'                       => $item->description ?? null,
                'unit_parcel_service_cost'          => $item->unit_parcel_service_cost ?? 0,
                'total_weight'                      => $item->total_weight,
                'total_cbm'                         => $item->total_cbm,
                'content_parcel'                    => $item->content_parcel ?? null,
            ]);
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    function percentage($totalAmount, $percentageAmount)
    {
        return $totalAmount * ($percentageAmount / 100);
    }

    //Parcel Events

    public function pickupdatemanAssigned($id, $request)
    {
        try {
            $pickupAsisgn                = new ParcelEvent();
            $pickupAsisgn->parcel_id     = $id;
            $pickupAsisgn->pickup_man_id = $request->delivery_man_id;
            $pickupAsisgn->note          = $request->note;
            $pickupAsisgn->parcel_status = ParcelStatus::PICKUP_ASSIGN;
            $pickupAsisgn->created_by    = Auth::user()->id;
            $pickupAsisgn->save();
            $parcel                      = Parcel::find($id);
            $parcel->status              = ParcelStatus::PICKUP_ASSIGN;
            $parcel->save();
            if ($request->send_sms_pickuman == 'on') {
                $msg = 'Dear ' . $pickupAsisgn->pickupman->user->name . ', Please pickup parcel with ID ' . $parcel->tracking_id . ' parcel from (' . $parcel->merchant->business_name . ',' . $parcel->merchant->user->mobile . ',' . $parcel->merchant->address . ') within ' . dateFormat($parcel->pickup_date) . ' -' . settings()->name;
                app(SmsService::class)->sendSms($pickupAsisgn->pickupman->user->mobile, $msg);
            }

            try {
                $msgNotification = 'Dear ' . $pickupAsisgn->pickupman->user->name . ', Please pickup parcel with ID ' . $parcel->tracking_id . ' parcel from (' . $parcel->merchant->business_name . ',' . $parcel->merchant->user->mobile . ',' . $parcel->merchant->address . ') within ' . dateFormat($parcel->pickup_date) . ' -' . settings()->name;
                app(PushNotificationService::class)->sendStatusPushNotificationTo($parcel, $pickupAsisgn->pickupman->user->device_token, $msgNotification, 'deliveryMan');
            } catch (\Exception $exception) {
            }

            if ($request->send_sms_merchant  == 'on') {
                $msg = 'Dear ' . $parcel->merchant->business_name . ', your  parcel with ID ' . $parcel->tracking_id . ' Pickup man assign from ' . settings()->name . '. Assign by ' . $pickupAsisgn->pickupman->user->name . ', ' . $pickupAsisgn->pickupman->user->mobile . ' Track here: ' . url('/') . ' -' . settings()->name;
                $response =  app(SmsService::class)->sendSms($parcel->merchant->user->mobile, $msg);
            }
            try {
                $msgNotification = 'Dear ' . $parcel->merchant->business_name . ', your  parcel with ID ' . $parcel->tracking_id . ' Pickup man assign from ' . settings()->name . '. Assign by ' . $pickupAsisgn->pickupman->user->name . ', ' . $pickupAsisgn->pickupman->user->mobile . ' Track here: ' . url('/') . ' -' . settings()->name;
                if (isset($parcel->merchant->user->web_token)) {
                    app(PushNotificationService::class)->sendStatusPushNotificationTo($parcel, $parcel->merchant->user->web_token, $msgNotification, 'merchant');
                }
                if (isset($parcel->merchant->user->device_token)) {
                    app(PushNotificationService::class)->sendStatusPushNotificationTo($parcel, $parcel->merchant->user->device_token, $msgNotification, 'merchant');
                }
            } catch (\Exception $exception) {
            }
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function readyToReassign($id, $request)
    {
        try {
            $pickupAsisgn                = new ParcelEvent();
            $pickupAsisgn->parcel_id     = $id;
            $pickupAsisgn->note          = $request->note;
            $pickupAsisgn->parcel_status = ParcelStatus::READY_TO_REASSIGN_REGULAR;
            $pickupAsisgn->created_by    = Auth::user()->id;
            $pickupAsisgn->save();
            $parcel                      = Parcel::find($id);
            $parcel->status              = ParcelStatus::READY_TO_REASSIGN_REGULAR;
            $parcel->save();

            if ($request->send_sms_merchant  == 'on') {
                $msg = 'Dear ' . $parcel->merchant->business_name . ', your  parcel with ID ' . $parcel->tracking_id . ' ready to reassign from ' . settings()->name . ' Track here: ' . url('/') . ' -' . settings()->name;
                app(SmsService::class)->sendSms($parcel->merchant->user->mobile, $msg);
            }
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function readyToReassignBooking($id, $request)
    {
        try {
            $pickupAsisgn                = new ParcelEvent();
            $pickupAsisgn->parcel_id     = $id;
            $pickupAsisgn->note          = $request->note;
            $pickupAsisgn->parcel_status = ParcelStatus::READY_TO_REASSIGN_BOOKING;
            $pickupAsisgn->created_by    = Auth::user()->id;
            $pickupAsisgn->save();
            $parcel                      = Parcel::find($id);
            $parcel->status              = ParcelStatus::READY_TO_REASSIGN_BOOKING;
            $parcel->save();

            if ($request->send_sms_merchant  == 'on') {
                $msg = 'Dear ' . $parcel->merchant->business_name . ', your  parcel with ID ' . $parcel->tracking_id . ' ready to reassign booking from ' . settings()->name . ' Track here: ' . url('/') . ' -' . settings()->name;
                app(SmsService::class)->sendSms($parcel->merchant->user->mobile, $msg);
            }
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function confirmedBooking($id, $request)
    {
        try {
            $pickupAsisgn                = new ParcelEvent();
            $pickupAsisgn->parcel_id     = $id;
            $pickupAsisgn->note          = $request->note;
            $pickupAsisgn->parcel_status = ParcelStatus::CONFIRMED_BOOKING;
            $pickupAsisgn->created_by    = Auth::user()->id;
            $pickupAsisgn->save();
            $parcel                      = Parcel::find($id);
            $parcel->status              = ParcelStatus::CONFIRMED_BOOKING;
            $parcel->save();

            if ($request->send_sms_merchant  == 'on') {
                $msg = 'Dear ' . $parcel->merchant->business_name . ', your  parcel with ID ' . $parcel->tracking_id . ' confirmed booking from ' . settings()->name . ' Track here: ' . url('/') . ' -' . settings()->name;
                app(SmsService::class)->sendSms($parcel->merchant->user->mobile, $msg);
            }
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function orderProcessing($id, $request)
    {
        try {
            $pickupAsisgn                = new ParcelEvent();
            $pickupAsisgn->parcel_id     = $id;
            $pickupAsisgn->note          = $request->note;
            $pickupAsisgn->parcel_status = ParcelStatus::PROCESSING;
            $pickupAsisgn->created_by    = Auth::user()->id;
            $pickupAsisgn->save();
            $parcel                      = Parcel::find($id);
            $parcel->status              = ParcelStatus::PROCESSING;
            $parcel->save();

            if ($request->send_sms_merchant  == 'on') {
                $msg = 'Dear ' . $parcel->merchant->business_name . ', your  parcel with ID ' . $parcel->tracking_id . ' processing from ' . settings()->name . ' Track here: ' . url('/') . ' -' . settings()->name;
                app(SmsService::class)->sendSms($parcel->merchant->user->mobile, $msg);
            }
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function PickupReSchedule($id, $request)
    {
        try {
            $date                            = Carbon::parse($request->date);
            $pickupReshcedule                = new ParcelEvent();
            $pickupReshcedule->parcel_id     = $id;
            $pickupReshcedule->pickup_man_id = $request->delivery_man_id;
            $pickupReshcedule->note          = $request->note;
            $pickupReshcedule->parcel_status = ParcelStatus::PICKUP_RE_SCHEDULE;
            $pickupReshcedule->created_by    = Auth::user()->id;
            $pickupReshcedule->save();
            $parcel                          = Parcel::find($id);
            $parcel->pickup_date             = $request->date;
            //Pickup & Delivery Time
            if ($parcel->delivery_type_id == DeliveryType::SAMEDAY) {
                if (date('H') < DeliveryTime::LAST_TIME) {
                    $parcel->delivery_date    = $request->date;
                } else {
                    $parcel->delivery_date    = $date->add(1, 'day')->format('Y-m-d');
                }
            } elseif ($parcel->delivery_type_id == DeliveryType::NEXTDAY) {
                if (date('H') < DeliveryTime::LAST_TIME) {
                    $parcel->delivery_date    = $date->add(1, 'day')->format('Y-m-d');
                } else {
                    $parcel->delivery_date    = $date->add(2, 'day')->format('Y-m-d');;
                }
            } elseif ($parcel->delivery_type_id == DeliveryType::SUBCITY) {
                if (date('H') < DeliveryTime::LAST_TIME) {
                    $parcel->delivery_date    = $date->add(DeliveryTime::SUBCITY, 'day')->format('Y-m-d');
                } else {
                    $parcel->delivery_date    = $date->add(DeliveryTime::SUBCITY + 1, 'day')->format('Y-m-d');
                }
            } elseif ($parcel->delivery_type_id == DeliveryType::OUTSIDECITY) {
                if (date('H') < DeliveryTime::LAST_TIME) {
                    $parcel->delivery_date    = $date->add(DeliveryTime::OUTSIDECITY, 'day')->format('Y-m-d');
                } else {
                    $parcel->delivery_date    = $date->add(DeliveryTime::OUTSIDECITY + 1, 'day')->format('Y-m-d');
                }
            }
            // End Pickup & Delivery Time
            $parcel->status = ParcelStatus::PICKUP_RE_SCHEDULE;
            $parcel->save();

            if ($request->send_sms_pickuman == 'on') {
                $msg = 'Dear ' . $pickupReshcedule->pickupman->user->name . ', Please pickup parcel with ID ' . $parcel->tracking_id . ' parcel from (' . $parcel->merchant->business_name . ',' . $parcel->merchant->user->mobile . ',' . $parcel->merchant->address . ') within ' . dateFormat($parcel->pickup_date) . ' -' . settings()->name;
                $response =  app(SmsService::class)->sendSms($pickupReshcedule->pickupman->user->mobile, $msg);
            }

            if ($request->send_sms_merchant  == 'on') {
                $msg = 'Dear' . $parcel->merchant->business_name . ', your  parcel with ID ' . $parcel->tracking_id . ' Pickup man assign from ' . settings()->name . '. Assign by' . $pickupReshcedule->pickupman->user->name . ', ' . $pickupReshcedule->pickupman->user->mobile . ' Track here: ' . url('/') . ' -' . settings()->name;
                $response =  app(SmsService::class)->sendSms($parcel->merchant->user->mobile, $msg);
            }

            try {
                if (isset($pickupReshcedule->pickupman->user->device_token)) {
                    $msgNotification = 'Dear ' . $pickupReshcedule->pickupman->user->name . ', Please pickup parcel with ID ' . $parcel->tracking_id . ' parcel from (' . $parcel->merchant->business_name . ',' . $parcel->merchant->user->mobile . ',' . $parcel->merchant->address . ') within ' . dateFormat($parcel->pickup_date) . ' -' . settings()->name;
                    app(PushNotificationService::class)->sendStatusPushNotificationTo($parcel, $pickupReshcedule->pickupman->user->device_token, $msgNotification, 'deliveryMan');
                }
            } catch (\Exception $exception) {
            }

            try {
                $msgNotification = 'Dear ' . $parcel->merchant->business_name . ', your  parcel with ID ' . $parcel->tracking_id . ' Pickup man assign from ' . settings()->name . '. Assign by ' . $pickupReshcedule->pickupman->user->name . ', ' . $pickupReshcedule->pickupman->user->mobile . ' Track here: ' . url('/') . ' -' . settings()->name;
                if (isset($parcel->merchant->user->web_token)) {
                    app(PushNotificationService::class)->sendStatusPushNotificationTo($parcel, $parcel->merchant->user->web_token, $msgNotification, 'merchant');
                }
                if (isset($parcel->merchant->user->device_token)) {
                    app(PushNotificationService::class)->sendStatusPushNotificationTo($parcel, $parcel->merchant->user->device_token, $msgNotification, 'merchant');
                }
            } catch (\Exception $exception) {
            }
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function receivedBypickupman($id, $request)
    {
        try {
            $receivedPickupman                = new ParcelEvent();
            $receivedPickupman->parcel_id     = $id;
            $receivedPickupman->note          = $request->note;
            $receivedPickupman->parcel_status = ParcelStatus::RECEIVED_BY_PICKUP_MAN;
            $receivedPickupman->created_by    = Auth::user()->id;
            $receivedPickupman->save();
            $parcel                           = Parcel::find($id);
            $parcel->status                   = ParcelStatus::RECEIVED_BY_PICKUP_MAN;
            $parcel->save();

            if ($request->send_sms_merchant  == 'on') {
                $msg = 'Dear ' . $receivedPickupman->pickupman->user->name . ', Please received parcel with ID ' . $parcel->tracking_id . ' parcel from (' . $parcel->merchant->business_name . ',' . $parcel->merchant->user->mobile . ',' . $parcel->merchant->address . ') within ' . dateFormat($parcel->pickup_date) . ' -' . settings()->name;
                $response =  app(SmsService::class)->sendSms($parcel->merchant->user->mobile, $msg);
            }

            try {
                $msgNotification = 'Dear ' . $receivedPickupman->pickupman->user->name . ', Please received parcel with ID ' . $parcel->tracking_id . ' parcel from (' . $parcel->merchant->business_name . ',' . $parcel->merchant->user->mobile . ',' . $parcel->merchant->address . ') within ' . dateFormat($parcel->pickup_date) . ' -' . settings()->name;
                app(PushNotificationService::class)->sendStatusPushNotificationTo($parcel, $receivedPickupman->pickupman->user->device_token, $msgNotification, 'deliveryMan');
            } catch (\Exception $exception) {
            }

            try {
                $msgNotification = 'Dear ' . $parcel->merchant->business_name . ', your  parcel with ID ' . $parcel->tracking_id . ' Pickup man assign from ' . settings()->name . '. Received by ' . $receivedPickupman->pickupman->user->name . ', ' . $receivedPickupman->pickupman->user->mobile . ' Track here: ' . url('/') . ' -' . settings()->name;
                if (isset($parcel->merchant->user->web_token)) {
                    app(PushNotificationService::class)->sendStatusPushNotificationTo($parcel, $parcel->merchant->user->web_token, $msgNotification, 'merchant');
                }
                if (isset($parcel->merchant->user->device_token)) {
                    app(PushNotificationService::class)->sendStatusPushNotificationTo($parcel, $parcel->merchant->user->device_token, $msgNotification, 'merchant');
                }
            } catch (\Exception $exception) {
                // dd($exception);
            }
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }


    public function deliveryManAssignMultipleParcel($request)
    {
        try {
            $deliveryUser  = DeliveryMan::find($request->delivery_man_id);
            foreach ($request->parcel_ids_ as $id) {
                $deliveryMan                           = new ParcelEvent();
                $deliveryMan->parcel_id                = $id;
                $deliveryMan->delivery_man_id          = $request->delivery_man_id;
                $deliveryMan->note                     = $request->note;
                $deliveryMan->delivery_lat             = $deliveryUser->delivery_lat;
                $deliveryMan->delivery_long            = $deliveryUser->delivery_long;
                $deliveryMan->parcel_status            = ParcelStatus::DELIVERY_MAN_ASSIGN;
                $deliveryMan->created_by               = Auth::user()->id;
                $deliveryMan->save();
                $parcel                                    = Parcel::find($id);
                $parcel->status                            = ParcelStatus::DELIVERY_MAN_ASSIGN;
                $parcel->save();
                if ($request->send_sms == 'on') {
                    $msg = 'Dear ' . $parcel->customer_name . ', parcel with ID ' . $parcel->tracking_id . ' from (' . $parcel->merchant->business_name . ')  ' . settings()->currency . ' (' . $parcel->cash_collection . ') delivery man assing by ' . $deliveryMan->deliveryMan->user->name . ', ' . $deliveryMan->deliveryMan->user->mobile . '. Track here:' . url('/') . '  -' . settings()->name;
                    $response =  app(SmsService::class)->sendSms($parcel->customer_phone, $msg);
                }
                try {
                    $msgNotification = 'Dear ' . $parcel->customer_name . ', parcel with ID ' . $parcel->tracking_id . ' from (' . $parcel->merchant->business_name . ')  ' . settings()->currency . ' (' . $parcel->cash_collection . ') delivery man assing by ' . $deliveryMan->deliveryMan->user->name . ', ' . $deliveryMan->deliveryMan->user->mobile . '. Track here:' . url('/') . '  -' . settings()->name;
                    app(PushNotificationService::class)->sendStatusPushNotificationTo($parcel, $deliveryMan->pickupman->user->device_token, $msgNotification, 'deliveryMan');
                } catch (\Exception $exception) {
                    // dd($exception);
                }
            }
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }


    public function deliverymanAssign($id, $request)
    {

        try {
            $deliveryUser  = DeliveryMan::find($request->delivery_man_id);
            $deliverymanAssign                  = new ParcelEvent();
            $deliverymanAssign->parcel_id       = $id;
            $deliverymanAssign->delivery_man_id = $request->delivery_man_id;
            $deliverymanAssign->note            = $request->note;
            $deliverymanAssign->delivery_lat    = $deliveryUser->delivery_lat;
            $deliverymanAssign->delivery_long   = $deliveryUser->delivery_long;
            $deliverymanAssign->parcel_status   = ParcelStatus::DELIVERY_MAN_ASSIGN;
            $deliverymanAssign->created_by      = Auth::user()->id;
            $deliverymanAssign->save();
            $parcel         = Parcel::find($id);
            $parcel->status = ParcelStatus::DELIVERY_MAN_ASSIGN;
            $parcel->delivery_man_id = $request->delivery_man_id;
            $parcel->save();
            if ($request->send_sms == 'on') {
                $msg = 'Dear ' . $parcel->customer_name . ', parcel with ID ' . $parcel->tracking_id . ' from (' . $parcel->merchant->business_name . ') TK(' . $parcel->cash_collection . ') delivery man assing by ' . $deliverymanAssign->deliveryMan->user->name . ', ' . $deliverymanAssign->deliveryMan->user->mobile . '. Track here:' . url('/') . '  -' . settings()->name;
                app(SmsService::class)->sendSms($parcel->customer_phone, $msg);
            }

            try {
                $msgNotification = 'Dear ' . $deliverymanAssign->deliveryMan->user->name . ', your  parcel with ID ' . $parcel->tracking_id . ' Track here: ' . url('/') . ' -' . settings()->name;
                app(PushNotificationService::class)->sendStatusPushNotificationTo($parcel, $deliverymanAssign->deliveryMan->user->device_token, $msgNotification, 'deliveryMan');
            } catch (\Exception $exception) {
                // dd($exception);
            }
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }


    public function deliveryReschedule($id, $request)
    {
        try {
            $deliveryReschedule                  = new ParcelEvent();
            $deliveryReschedule->parcel_id       = $id;
            $deliveryReschedule->delivery_man_id = $request->delivery_man_id;
            $deliveryReschedule->note            = $request->note;
            $deliveryReschedule->parcel_status   = ParcelStatus::DELIVERY_RE_SCHEDULE;
            $deliveryReschedule->created_by      = Auth::user()->id;
            $deliveryReschedule->save();
            $parcel                = Parcel::find($id);
            $parcel->delivery_date = $request->date;
            $parcel->status        = ParcelStatus::DELIVERY_RE_SCHEDULE;
            $parcel->delivery_man_id = $request->delivery_man_id;
            $parcel->save();

            if ($request->send_sms == 'on') {
                $msg = 'Dear ' . $parcel->customer_name . ', Your  parcel with ID ' . $parcel->tracking_id . '  is re-schedule  from (' . $parcel->merchant->business_name . ')  ' . settings()->currency . ' (' . $parcel->cash_collection . ') delivery man assign by ' . $deliveryReschedule->deliveryMan->user->name . ', ' . $deliveryReschedule->deliveryMan->user->mobile . '. Track here:' . url('/') . '  -' . settings()->name;
                app(SmsService::class)->sendSms($parcel->customer_phone, $msg);
            }

            try {
                $msgNotification = 'Dear ' . $deliveryReschedule->deliveryMan->user->name . ', your  parcel with ID ' . $parcel->tracking_id . ' Track here: ' . url('/') . ' -' . settings()->name;
                app(PushNotificationService::class)->sendStatusPushNotificationTo($parcel, $deliveryReschedule->deliveryMan->user->device_token, $msgNotification, 'deliveryMan');
            } catch (\Exception $exception) {
                // dd($exception);
            }
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }


    public function receivedWarehouse($id, $request)
    {
        try {
            DB::beginTransaction();
            $receivedWarehouse                 = new ParcelEvent();
            $receivedWarehouse->parcel_id      = $id;
            $receivedWarehouse->note           = $request->note;
            $receivedWarehouse->parcel_status  = ParcelStatus::RECEIVED_WAREHOUSE;
            $receivedWarehouse->created_by     = Auth::user()->id;
            $receivedWarehouse->save();
            $parcel                   = Parcel::find($id);
            //pickup charge
            $pickupreschedule = ParcelEvent::where('parcel_id', $id)->where('parcel_status', ParcelStatus::PICKUP_RE_SCHEDULE)->first();

            if ($pickupreschedule) {
                $deliveryManStatement                       = new DeliverymanStatement();
                $deliveryManStatement->parcel_id            = $id;
                $deliveryManStatement->delivery_man_id      = $pickupreschedule->pickupman->id;
                $deliveryManStatement->amount               = $pickupreschedule->pickupman->pickup_charge;
                $deliveryManStatement->type                 = StatementType::INCOME;
                $deliveryManStatement->date                 = date('Y-m-d H:i:s');
                $deliveryManStatement->note                 = __('statementNote.received_warehouse_deliveryman_statement');
                $deliveryManStatement->save();
                //pickup man balance add
                if ($deliveryManStatement) {
                    $pickupman                     = DeliveryMan::find($pickupreschedule->pickupman->id);
                    $pickupman->current_balance    = $pickupman->current_balance + $deliveryManStatement->amount;
                    $pickupman->save();
                }
                $courierStatement                       = new CourierStatement();
                $courierStatement->parcel_id            = $id;
                $courierStatement->delivery_man_id      = $deliveryManStatement->delivery_man_id;
                $courierStatement->amount               = $deliveryManStatement->amount;
                $courierStatement->type                 = StatementType::EXPENSE;
                $courierStatement->date                 = date('Y-m-d H:i:s');
                $courierStatement->note                 = __('statementNote.received_warehouse_courier_statement');
                $courierStatement->save();
            } else {
                $pickupAssign = ParcelEvent::where('parcel_id', $id)->where('parcel_status', ParcelStatus::PICKUP_ASSIGN)->first();
                $deliveryManStatement                       = new DeliverymanStatement();
                $deliveryManStatement->parcel_id            = $id;
                $deliveryManStatement->delivery_man_id      = $pickupAssign->pickupman->id;
                $deliveryManStatement->amount               = $pickupAssign->pickupman->pickup_charge;
                $deliveryManStatement->type                 = StatementType::INCOME;
                $deliveryManStatement->date                 = date('Y-m-d H:i:s');
                $deliveryManStatement->note                 = __('statementNote.received_warehouse_deliveryman_statement');
                $deliveryManStatement->save();
                //pickup man balance add
                if ($deliveryManStatement) {
                    $pickupman                     = DeliveryMan::find($pickupAssign->pickupman->id);
                    $pickupman->current_balance    = $pickupman->current_balance + $deliveryManStatement->amount;
                    $pickupman->save();
                }
                $courierStatement                       = new CourierStatement();
                $courierStatement->parcel_id            = $id;
                $courierStatement->delivery_man_id      = $deliveryManStatement->delivery_man_id;
                $courierStatement->amount               = $deliveryManStatement->amount;
                $courierStatement->type                 = StatementType::EXPENSE;
                $courierStatement->date                 = date('Y-m-d H:i:s');
                $courierStatement->note                 = __('statementNote.received_warehouse_courier_statement');
                $courierStatement->save();
            }

            $parcel->status = ParcelStatus::RECEIVED_WAREHOUSE;
            $parcel->save();

            DB::commit();

            if ($request->send_sms_customer == 'on') {
                $msg = 'Dear ' . $parcel->customer_name . ', we received a parcel with ID ' . $parcel->tracking_id . ' from (' . $parcel->merchant->business_name . ') and will deliver as soon as possible. Track here:' . url('/') . '  -' . settings()->name;
                $response =  app(SmsService::class)->sendSms($parcel->customer_phone, $msg);
            }

            if ($request->send_sms_merchant  == 'on') {
                $msg = 'Dear ' . $parcel->merchant->business_name . ', your  parcel with ID ' . $parcel->tracking_id . ' Received to Warehouse ' . $receivedWarehouse->hub->name . '. Track here: ' . url('/') . ' -' . settings()->name;
                $response =  app(SmsService::class)->sendSms($parcel->merchant->user->mobile, $msg);
            }

            try {
                $msgNotification = 'Dear ' . $parcel->merchant->business_name . ', your  parcel with ID ' . $parcel->tracking_id . ' Received to Warehouse ' . $receivedWarehouse->hub->name . '. Track here: ' . url('/') . ' -' . settings()->name;
                if (isset($parcel->merchant->user->web_token)) {
                    app(PushNotificationService::class)->sendStatusPushNotificationTo($parcel, $parcel->merchant->user->web_token, $msgNotification, 'merchant');
                }
                if (isset($parcel->merchant->user->device_token)) {
                    app(PushNotificationService::class)->sendStatusPushNotificationTo($parcel, $parcel->merchant->user->device_token, $msgNotification, 'merchant');
                }
            } catch (\Exception $exception) {
                // dd($exception);
            }
            return true;
        } catch (\Throwable $th) {
            DB::rollBack();
            return false;
        }
    }


    public function receivedWarehouseCancel($id, $request)
    {
        try {
            DB::beginTransaction();
            $parcel = Parcel::find($id);
            if ($parcel->status == ParcelStatus::RECEIVED_WAREHOUSE) {
                $pickupAsisgn  = ParcelEvent::where(['parcel_id' => $id, 'parcel_status' => $parcel->status])->first();
                ParcelEvent::destroy($pickupAsisgn->id);
            }

            $receivedPickupman = ParcelEvent::where(['parcel_id' => $id, 'parcel_status' => ParcelStatus::RECEIVED_BY_PICKUP_MAN])->first();
            $pickupreschedule   = ParcelEvent::where(['parcel_id' => $id, 'parcel_status' => ParcelStatus::PICKUP_RE_SCHEDULE])->first();
            $pickupAssign      = ParcelEvent::where(['parcel_id' => $id, 'parcel_status' => ParcelStatus::PICKUP_ASSIGN])->first();

            if ($pickupreschedule) {
                $deliveryManStatement                       = new DeliverymanStatement();
                $deliveryManStatement->parcel_id            = $id;
                $deliveryManStatement->delivery_man_id      = $pickupreschedule->pickupman->id;
                $deliveryManStatement->amount               = $pickupreschedule->pickupman->pickup_charge;
                $deliveryManStatement->type                 = StatementType::EXPENSE;
                $deliveryManStatement->date                 = date('Y-m-d H:i:s');
                $deliveryManStatement->note                 = __('statementNote.received_warehouse_deliveryman_statement_cancel');
                $deliveryManStatement->save();
                //pickup man balance add
                if ($deliveryManStatement) {
                    $pickupman                     = DeliveryMan::find($pickupreschedule->pickupman->id);
                    $pickupman->current_balance    = $pickupman->current_balance - $deliveryManStatement->amount;
                    $pickupman->save();
                }
                $courierStatement                       = new CourierStatement();
                $courierStatement->parcel_id            = $id;
                $courierStatement->delivery_man_id      = $deliveryManStatement->delivery_man_id;
                $courierStatement->amount               = $deliveryManStatement->amount;
                $courierStatement->type                 = StatementType::INCOME;
                $courierStatement->date                 = date('Y-m-d H:i:s');
                $courierStatement->note                 = __('statementNote.received_warehouse_courier_statement_cancel');
                $courierStatement->save();
            } else {
                $deliveryManStatement                       = new DeliverymanStatement();
                $deliveryManStatement->parcel_id            = $id;
                $deliveryManStatement->delivery_man_id      = $pickupAssign->pickupman->id;
                $deliveryManStatement->amount               = $pickupAssign->pickupman->pickup_charge;
                $deliveryManStatement->type                 = StatementType::EXPENSE;
                $deliveryManStatement->date                 = date('Y-m-d H:i:s');
                $deliveryManStatement->note                 = __('statementNote.received_warehouse_deliveryman_statement_cancel');
                $deliveryManStatement->save();
                //pickup man balance
                if ($deliveryManStatement) {
                    $pickupman                     = DeliveryMan::find($pickupAssign->pickupman->id);
                    $pickupman->current_balance    = $pickupman->current_balance - $deliveryManStatement->amount;
                    $pickupman->save();
                }
                $courierStatement                       = new CourierStatement();
                $courierStatement->parcel_id            = $id;
                $courierStatement->delivery_man_id      = $deliveryManStatement->delivery_man_id;
                $courierStatement->amount               = $deliveryManStatement->amount;
                $courierStatement->type                 = StatementType::INCOME;
                $courierStatement->date                 = date('Y-m-d H:i:s');
                $courierStatement->note                 = __('statementNote.received_warehouse_courier_statement_cancel');
                $courierStatement->save();
            }

            if ($receivedPickupman) {
                $parcel->status = ParcelStatus::RECEIVED_BY_PICKUP_MAN;
            } elseif ($pickupreschedule) {
                $parcel->status = ParcelStatus::PICKUP_RE_SCHEDULE;
            } else {
                $parcel->status = ParcelStatus::PICKUP_ASSIGN;
            }
            $parcel->save();
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollBack();
            return false;
        }
    }

    public function returntoQourier($id, $request)
    {
        try {
            $returntocourier                = new ParcelEvent();
            $returntocourier->parcel_id     = $id;
            $returntocourier->note          = $request->note;
            $returntocourier->parcel_status = ParcelStatus::RETURN_TO_COURIER;
            $returntocourier->created_by    = Auth::user()->id;
            $returntocourier->save();
            $parcel         = Parcel::find($id);
            $parcel->status = ParcelStatus::RETURN_TO_COURIER;
            $parcel->save();
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }


    public function returntoQourierCancel($id, $request)
    {
        try {
            $parcel = Parcel::find($id);
            if ($parcel->status == ParcelStatus::RETURN_TO_COURIER) {
                ParcelEvent::where(['parcel_id' => $id, 'parcel_status' => $parcel->status])->delete();
                $deliverymanReschedule = ParcelEvent::where(['parcel_id' => $id, 'parcel_status' => ParcelStatus::DELIVERY_RE_SCHEDULE])->get();
            }
            if ($deliverymanReschedule) {
                $parcel->status   = ParcelStatus::DELIVERY_RE_SCHEDULE;
            } else {
                $parcel->status   = ParcelStatus::DELIVERY_MAN_ASSIGN;
            }
            $parcel->save();
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function returnAssignToMerchant($id, $request)
    {
        try {
            DB::beginTransaction();
            $returnAssignToMerchant                  = new ParcelEvent();
            $returnAssignToMerchant->parcel_id       = $id;
            $returnAssignToMerchant->delivery_man_id = $request->delivery_man_id;
            $returnAssignToMerchant->note            = $request->note;
            $returnAssignToMerchant->parcel_status   = ParcelStatus::RETURN_ASSIGN_TO_MERCHANT;
            $returnAssignToMerchant->created_by      = Auth::user()->id;
            $returnAssignToMerchant->save();

            // Delivery man current balance update
            $deliveryMan                              = DeliveryMan::find($request->delivery_man_id);

            // Courier statement
            $deliveryManStatement                       = new DeliverymanStatement();
            $deliveryManStatement->parcel_id            = $id;
            $deliveryManStatement->delivery_man_id      = $request->delivery_man_id;
            $deliveryManStatement->amount               = $deliveryMan->return_charge;
            $deliveryManStatement->type                 = StatementType::INCOME;
            $deliveryManStatement->date                 = date('Y-m-d H:i:s');
            $deliveryManStatement->note                 = __('statementNote.returned_to_merchant_income');
            $deliveryManStatement->save();
            // Courier statement
            $courierStatement                           = new CourierStatement();
            $courierStatement->parcel_id                = $id;
            $courierStatement->delivery_man_id          = $request->delivery_man_id;
            $courierStatement->amount                   = $deliveryMan->return_charge;
            $courierStatement->type                     = StatementType::EXPENSE;
            $courierStatement->date                     = date('Y-m-d H:i:s');
            $courierStatement->note                     = __('statementNote.returned_to_merchant_expense');
            $courierStatement->save();
            // End

            $parcel = Parcel::find($id);
            $parcel->delivery_date = $request->date;
            $parcel->status        = ParcelStatus::RETURN_ASSIGN_TO_MERCHANT;
            $parcel->save();
            DB::commit();
            if ($request->send_sms == 'on') {
                $msg = 'Dear ' . $parcel->merchant->business_name . ', parcel with ID ' . $parcel->tracking_id . ' is return to you by ' . $returnAssignToMerchant->deliveryMan->user->name . ', ' . $returnAssignToMerchant->deliveryMan->user->mobile . '. visit:' . url('/') . '  -' . settings()->name;
                $response =  app(SmsService::class)->sendSms($parcel->merchant->user->mobile, $msg);
            }

            try {
                $msgNotification = 'Dear ' . $parcel->merchant->business_name . ', parcel with ID ' . $parcel->tracking_id . ' is return to you by ' . $returnAssignToMerchant->deliveryMan->user->name . ', ' . $returnAssignToMerchant->deliveryMan->user->mobile . '. visit:' . url('/') . '  -' . settings()->name;
                if (isset($parcel->merchant->user->web_token)) {
                    app(PushNotificationService::class)->sendStatusPushNotificationTo($parcel, $parcel->merchant->user->web_token, $msgNotification, 'merchant');
                }
                if (isset($parcel->merchant->user->device_token)) {
                    app(PushNotificationService::class)->sendStatusPushNotificationTo($parcel, $parcel->merchant->user->device_token, $msgNotification, 'merchant');
                }
            } catch (\Exception $exception) {
                // dd($exception);
            }
            return true;
        } catch (\Throwable $th) {
            DB::rollBack();
            return false;
        }
    }

    public function returnAssignToMerchantCancel($id, $request)
    {
        try {
            DB::beginTransaction();
            $parcel = Parcel::find($id);
            if ($parcel->status == ParcelStatus::RETURN_ASSIGN_TO_MERCHANT) {
                $event = ParcelEvent::where(['parcel_id' => $id, 'parcel_status' => $parcel->status])->first();
                ParcelEvent::destroy($event->id);
                // Delivery man current balance update
                $deliveryMan                                = DeliveryMan::find($event->delivery_man_id);
                $deliveryMan->current_balance               = $deliveryMan->current_balance - $deliveryMan->return_charge;
                $deliveryMan->save();
                // Courier statement
                $deliveryManStatement                       = new DeliverymanStatement();
                $deliveryManStatement->parcel_id            = $id;
                $deliveryManStatement->delivery_man_id      = $event->delivery_man_id;
                $deliveryManStatement->amount               = $deliveryMan->return_charge;
                $deliveryManStatement->type                 = StatementType::EXPENSE;
                $deliveryManStatement->date                 = date('Y-m-d H:i:s');
                $deliveryManStatement->note                 = __('statementNote.returned_to_merchant_expense_cancel');
                $deliveryManStatement->save();
                // Courier statement
                $courierStatement                           = new CourierStatement();
                $courierStatement->parcel_id                = $id;
                $courierStatement->delivery_man_id          = $event->delivery_man_id;
                $courierStatement->amount                   = $deliveryMan->return_charge;
                $courierStatement->type                     = StatementType::INCOME;
                $courierStatement->date                     = date('Y-m-d H:i:s');
                $courierStatement->note                     = __('statementNote.returned_to_merchant_income_cancel');
                $courierStatement->save();
                // End
            }
            $parcel->status = ParcelStatus::RETURN_TO_COURIER;
            $parcel->save();
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollBack();
            return false;
        }
    }

    public function returnAssignToMerchantReschedule($id, $request)
    {
        try {
            $returnassigntomerchant                  = new ParcelEvent();
            $returnassigntomerchant->parcel_id       = $id;
            $returnassigntomerchant->delivery_man_id = $request->delivery_man_id;
            $returnassigntomerchant->note            = $request->note;
            $returnassigntomerchant->parcel_status   = ParcelStatus::RETURN_MERCHANT_RE_SCHEDULE;
            $returnassigntomerchant->created_by      = Auth::user()->id;
            $returnassigntomerchant->save();
            $parcel                = Parcel::find($id);
            $parcel->delivery_date = $request->date;
            $parcel->status        = ParcelStatus::RETURN_MERCHANT_RE_SCHEDULE;
            $parcel->save();

            if ($request->send_sms == 'on') {
                $msg = 'Dear ' . $parcel->merchant->business_name . ', parcel with ID ' . $parcel->tracking_id . ' is return to you by ' . $returnassigntomerchant->deliveryMan->user->name . ', ' . $returnassigntomerchant->deliveryMan->user->mobile . '. visit: ' . url('/') . '  -' . settings()->name;
                $response =  app(SmsService::class)->sendSms($parcel->merchant->user->mobile, $msg);
            }
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function returnAssignToMerchantRescheduleCancel($id, $request)
    {
        try {
            $parcel = Parcel::find($id);
            if ($parcel->status == ParcelStatus::RETURN_MERCHANT_RE_SCHEDULE) {
                ParcelEvent::where(['parcel_id' => $id, 'parcel_status' => $parcel->status])->delete();
            }
            $parcel->status  = ParcelStatus::RETURN_ASSIGN_TO_MERCHANT;
            $parcel->save();
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function returnReceivedByMerchant($id, $request)
    {
        try {
            $returnReceived                 = new ParcelEvent();
            $returnReceived->parcel_id      = $id;
            $returnReceived->note           = $request->note;
            $returnReceived->parcel_status  = ParcelStatus::RETURN_RECEIVED_BY_MERCHANT;
            $returnReceived->created_by     = Auth::user()->id;
            $returnReceived->save();
            $parcel                         = Parcel::find($id);
            //delivery charge
            $reScheduleDeliveryman           = ParcelEvent::Where('parcel_id', $id)->where('parcel_status', ParcelStatus::RETURN_MERCHANT_RE_SCHEDULE)->first();
            $deliveryManAssign = ParcelEvent::where('parcel_id', $id)->where('parcel_status', ParcelStatus::RETURN_ASSIGN_TO_MERCHANT)->first();
            if ($reScheduleDeliveryman) {
                $deliveryManStatement                       = new DeliverymanStatement();
                $deliveryManStatement->parcel_id            = $id;
                $deliveryManStatement->delivery_man_id      = $reScheduleDeliveryman->deliveryMan->id;
                $deliveryManStatement->amount               = $reScheduleDeliveryman->deliveryMan->return_charge;
                $deliveryManStatement->type                 = StatementType::INCOME;
                $deliveryManStatement->date                 = date('Y-m-d H:i:s');
                $deliveryManStatement->note                 = __('statementNote.return_to_merchant_deliveryman_statement');
                $deliveryManStatement->save();
                //delivery man balance add
                if ($deliveryManStatement) {
                    $deliveryMan                        = DeliveryMan::find($reScheduleDeliveryman->deliveryMan->id);
                    $deliveryMan->current_balance       = ($deliveryMan->current_balance + $deliveryManStatement->amount);
                    $deliveryMan->save();
                }
                $courierStatement                       = new CourierStatement();
                $courierStatement->parcel_id            = $id;
                $courierStatement->delivery_man_id      = $deliveryManStatement->delivery_man_id;
                $courierStatement->amount               = $deliveryManStatement->amount;
                $courierStatement->type                 = StatementType::EXPENSE;
                $courierStatement->date                 = date('Y-m-d H:i:s');
                $courierStatement->note                 = __('statementNote.return_to_merchant__deliveryman_statement');
                $courierStatement->save();
            } elseif ($deliveryManAssign) {
                $deliveryManStatement                       = new DeliverymanStatement();
                $deliveryManStatement->parcel_id            = $id;
                $deliveryManStatement->delivery_man_id      = $deliveryManAssign->deliveryMan->id;
                $deliveryManStatement->amount               = $deliveryManAssign->deliveryMan->return_charge;
                $deliveryManStatement->type                 = StatementType::INCOME;
                $deliveryManStatement->date                 = date('Y-m-d H:i:s');
                $deliveryManStatement->note                 = __('statementNote.return_to_merchant_deliveryman_statement');
                $deliveryManStatement->save();
                //delivery man balance add
                if ($deliveryManStatement) {
                    $deliveryMan                            = DeliveryMan::find($deliveryManAssign->deliveryMan->id);
                    $deliveryMan->current_balance           = ($deliveryMan->current_balance + $deliveryManStatement->amount);
                    $deliveryMan->save();
                }

                $courierStatement                       = new CourierStatement();
                $courierStatement->parcel_id            = $id;
                $courierStatement->delivery_man_id      = $deliveryManStatement->delivery_man_id;
                $courierStatement->amount               = $deliveryManStatement->amount;
                $courierStatement->type                 = StatementType::EXPENSE;
                $courierStatement->date                 = date('Y-m-d H:i:s');
                $courierStatement->note                 = __('statementNote.return_to_merchant_deliveryman_statement');
                $courierStatement->save();
            }
            //total delivery charge
            $merchant = Merchant::find($parcel->merchant_id);
            $merchantStatement                   = new MerchantStatement();
            $merchantStatement->parcel_id        = $id;
            if ($parcel->status == ParcelStatus::RETURN_ASSIGN_TO_MERCHANT || $parcel->status == ParcelStatus::RETURN_MERCHANT_RE_SCHEDULE) {
                $merchantStatement->delivery_man_id  = $deliveryManStatement->delivery_man_id;
            } else {
                $merchantStatement->delivery_man_id  = null;
            }
            $merchantStatement->amount           = ($parcel->delivery_charge / 100) * $merchant->return_charges;
            $merchantStatement->type             = StatementType::EXPENSE;
            $merchantStatement->date             =  date('Y-m-d H:i:s');
            $merchantStatement->note             = __('statementNote.return_received_by_merchant_statment');
            $merchantStatement->save();
            // total charge minus from merchant current balance
            $merchantCost = Merchant::find($parcel->merchant_id);
            //return delivery charge calculation
            $return_delivery_charge = ($parcel->delivery_charge / 100) * $merchantCost->return_charges;
            //end return  delivery charge calculation
            $current = ((float)$merchantCost->current_balance - $return_delivery_charge);
            $merchantCost->current_balance = $current;
            $merchantCost->save();
            //end merchant expense vat + total charge amount
            //courier statement
            $courier_statement                  = new CourierStatement();
            $courier_statement->parcel_id       = $id;
            $courier_statement->delivery_man_id = @$merchantStatement->delivery_man_id;
            $courier_statement->amount          = $return_delivery_charge;
            $courier_statement->type            = StatementType::INCOME;
            $courier_statement->date            = date('Y-m-d H:i:s');
            $courier_statement->note            = __('statementNote.return_received_by_statement');
            $courier_statement->save();
            $parcel->return_charges = $return_delivery_charge;
            $parcel->return_date_time = date('Y-m-d H:i:s');
            $parcel->status = ParcelStatus::RETURN_RECEIVED_BY_MERCHANT;
            $parcel->is_returned    = BooleanStatus::YES; // parcel is returned
            $parcel->save();
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function returnReceivedByMerchantCancel($id, $request)
    {
        try {
            $parcel = Parcel::find($id);
            if ($parcel->status == ParcelStatus::RETURN_RECEIVED_BY_MERCHANT) {
                $pickupAsisgn = ParcelEvent::where(['parcel_id' => $id, 'parcel_status' => $parcel->status])->first();
                ParcelEvent::destroy($pickupAsisgn->id);
            }
            $returnreschedule     = ParcelEvent::where(['parcel_id' => $id, 'parcel_status' => ParcelStatus::RETURN_MERCHANT_RE_SCHEDULE])->first();
            if ($returnreschedule) {
                $parcel->status   = ParcelStatus::RETURN_MERCHANT_RE_SCHEDULE;
            } else {
                $parcel->status   = ParcelStatus::RETURN_ASSIGN_TO_MERCHANT;
            }
            $parcel->is_returned    = BooleanStatus::NO; // parcel  returned cancel
            $parcel->save();
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function parcelDelivered($id, $request)
    {
        try {
            $parcel = Parcel::find($id);
            if (!$parcel) {
                return false;
            }

            $whoPays = (int) ($parcel->who_pays_either ?? 0);
            $paymentStatus = (int) ($parcel->payment_status ?? 0);
            if ($paymentStatus !== InvoiceStatus::PAID) {
                return false;
            }

            $parcelDelivered                = new ParcelEvent();
            $parcelDelivered->parcel_id     = $id;
            $parcelDelivered->note          = $request->note;
            $parcelDelivered->parcel_status = ParcelStatus::DELIVERED;
            $parcelDelivered->created_by    = Auth::user()->id;
            $parcelDelivered->save();
            if ($parcel) {
                $proofUpdated = false;
                if ($request->hasFile('delivery_proof_image')) {
                    $uploadId = $this->storeDeliveryProofUpload($request->file('delivery_proof_image'));
                    if ($uploadId) {
                        $parcel->delivery_proof_image_id = $uploadId;
                        $proofUpdated = true;
                    }
                }
                if ($request->filled('delivery_proof_lat')) {
                    $parcel->delivery_proof_lat = $request->delivery_proof_lat;
                    $proofUpdated = true;
                }
                if ($request->filled('delivery_proof_lng')) {
                    $parcel->delivery_proof_lng = $request->delivery_proof_lng;
                    $proofUpdated = true;
                }
                if ($proofUpdated && !$parcel->delivery_proof_timestamp) {
                    $parcel->delivery_proof_timestamp = now();
                }
                if ($proofUpdated) {
                    $parcel->save();
                }
            }
            //delivery charge
            $reSceduleDeliveryman           = ParcelEvent::Where('parcel_id', $id)->where('parcel_status', ParcelStatus::DELIVERY_RE_SCHEDULE)->get()->last();
            if ($reSceduleDeliveryman) {
                $deliveryManStatement                       = new DeliverymanStatement();
                $deliveryManStatement->parcel_id            = $id;
                $deliveryManStatement->delivery_man_id      = $reSceduleDeliveryman->deliveryMan->id;
                $deliveryManStatement->amount               = $reSceduleDeliveryman->deliveryMan->delivery_charge;
                $deliveryManStatement->type                 = StatementType::INCOME;
                $deliveryManStatement->date                 = date('Y-m-d H:i:s');
                $deliveryManStatement->note                 = __('statementNote.delivered_deliveryman_statement');
                $deliveryManStatement->save();
                //delivery man balance add
                if ($deliveryManStatement) {
                    $deliveryMan                        = DeliveryMan::find($reSceduleDeliveryman->deliveryMan->id);
                    $deliveryMan->current_balance       = $deliveryMan->current_balance + $deliveryManStatement->amount;
                    $deliveryMan->save();
                }

                $courierStatement                       = new CourierStatement();
                $courierStatement->parcel_id            = $id;
                $courierStatement->delivery_man_id      = $deliveryManStatement->delivery_man_id;
                $courierStatement->amount               = $deliveryManStatement->amount;
                $courierStatement->type                 = StatementType::EXPENSE;
                $courierStatement->date                 = date('Y-m-d H:i:s');
                $courierStatement->note                 = __('statementNote.delivered_deliveryman_statement');
                $courierStatement->save();
                //cash collection income from customer for store (- amount)
                $deliveryManStatement                       = new DeliverymanStatement();
                $deliveryManStatement->parcel_id            = $id;
                $deliveryManStatement->delivery_man_id      = $reSceduleDeliveryman->deliveryMan->id;
                $deliveryManStatement->amount               = ($parcel->cash_collection);
                $deliveryManStatement->cash_collection      = 1;
                $deliveryManStatement->type                 = StatementType::EXPENSE;
                $deliveryManStatement->date                 = date('Y-m-d H:i:s');
                $deliveryManStatement->note                 = __('statementNote.delivered_deliveryman_statement');
                $deliveryManStatement->save();

                //cash collection added  delivery man balance
                $deliveryManBalance                                = DeliveryMan::find($deliveryMan->id);
                $deliveryManBalance->current_balance               = $deliveryManBalance->current_balance + (-$parcel->cash_collection);
                $deliveryManBalance->save();
            } else {
                $deliveryManAssign = ParcelEvent::where('parcel_id', $id)->where('parcel_status', ParcelStatus::DELIVERY_MAN_ASSIGN)->first();
                $deliveryManStatement                       = new DeliverymanStatement();
                $deliveryManStatement->parcel_id            = $id;
                $deliveryManStatement->delivery_man_id      = $deliveryManAssign->deliveryMan->id;
                $deliveryManStatement->amount               = $deliveryManAssign->deliveryMan->delivery_charge;
                $deliveryManStatement->type                 = StatementType::INCOME;
                $deliveryManStatement->date                 = date('Y-m-d H:i:s');
                $deliveryManStatement->note                 = __('statementNote.delivered_deliveryman_statement');
                $deliveryManStatement->save();
                //delivery man balance add
                if ($deliveryManStatement) {
                    $deliveryMan                            = DeliveryMan::find($deliveryManAssign->deliveryMan->id);
                    $deliveryMan->current_balance           = $deliveryMan->current_balance + $deliveryManStatement->amount;
                    $deliveryMan->save();
                }
                $courierStatement                       = new CourierStatement();
                $courierStatement->parcel_id            = $id;
                $courierStatement->delivery_man_id      = $deliveryManStatement->delivery_man_id;
                $courierStatement->amount               = $deliveryManStatement->amount;
                $courierStatement->type                 = StatementType::EXPENSE;
                $courierStatement->date                 = date('Y-m-d H:i:s');
                $courierStatement->note                 = __('statementNote.delivered_deliveryman_statement');
                $courierStatement->save();
                //cash collection income from customer for store (- amount)
                $deliveryManStatement                       = new DeliverymanStatement();
                $deliveryManStatement->parcel_id            = $id;
                $deliveryManStatement->delivery_man_id      = $deliveryManAssign->deliveryMan->id;
                $deliveryManStatement->amount               = ($parcel->cash_collection);
                $deliveryManStatement->cash_collection      = 1;
                $deliveryManStatement->type                 = StatementType::EXPENSE;
                $deliveryManStatement->date                 = date('Y-m-d H:i:s');
                $deliveryManStatement->note                 = __('statementNote.delivered_deliveryman_statement');
                $deliveryManStatement->save();
                //cash collection added  delivery man balance
                $deliveryManBalance                                = DeliveryMan::find($deliveryMan->id);
                $deliveryManBalance->current_balance               = $deliveryManBalance->current_balance + (-$parcel->cash_collection);
                $deliveryManBalance->save();
            }


            //merchant statment
            $merchantStatement                   = new MerchantStatement();
            $merchantStatement->parcel_id        = $id;
            $merchantStatement->delivery_man_id  = $deliveryManStatement->delivery_man_id;
            $merchantStatement->amount           = $parcel->cash_collection;
            $merchantStatement->type             = StatementType::INCOME;
            $merchantStatement->date             =  date('Y-m-d H:i:s');
            $merchantStatement->note             = __('statementNote.delivered_merchant_statment');
            $merchantStatement->save();

            //merchant balance add
            if ($merchantStatement) {
                $merchant = Merchant::find($parcel->merchant_id);
                $merchant->current_balance = $merchant->current_balance + $parcel->cash_collection;
                $merchant->save();
            }

            //merchant expense vat + total charge amount
            //total delivery charge
            $merchantStatement                   = new MerchantStatement();
            $merchantStatement->parcel_id        = $id;
            $merchantStatement->delivery_man_id  = $deliveryManStatement->delivery_man_id;
            $merchantStatement->amount           = $parcel->total_delivery_amount;
            $merchantStatement->type             = StatementType::EXPENSE;
            $merchantStatement->date             =  date('Y-m-d H:i:s');
            $merchantStatement->note             = __('statementNote.delivered_merchant_statment');
            $merchantStatement->save();

            //vat
            $merchantStatement                   = new MerchantStatement();
            $merchantStatement->parcel_id        = $id;
            $merchantStatement->delivery_man_id  = $deliveryManStatement->delivery_man_id;
            $merchantStatement->amount           = $parcel->vat_amount;
            $merchantStatement->type             = StatementType::EXPENSE;
            $merchantStatement->date             =  date('Y-m-d H:i:s');
            $merchantStatement->note             = __('statementNote.delivered_merchant_statment');
            $merchantStatement->save();

            //vat and total charge minus from merchant current balance
            $deliveryCost = $parcel->total_delivery_amount + $parcel->vat_amount;
            $merchantCost = Merchant::find($parcel->merchant_id);
            $merchantCost->current_balance = $merchantCost->current_balance - $deliveryCost;
            $merchantCost->save();

            //end merchant expense vat + total charge amount


            //courier statement
            $courier_statement                  = new CourierStatement();
            $courier_statement->parcel_id       = $id;
            $courier_statement->delivery_man_id = $merchantStatement->delivery_man_id;
            $courier_statement->amount          = $parcel->total_delivery_amount;
            $courier_statement->type            = StatementType::INCOME;
            $courier_statement->date            = date('Y-m-d H:i:s');
            $courier_statement->note            = __('statementNote.delivered_merchant_courier_statement');
            $courier_statement->save();

            // Vat statement
            $vat                            = new VatStatement();
            $vat->parcel_id                 = $id;
            $vat->amount                    = $parcel->vat_amount;
            $vat->type                      = StatementType::INCOME;
            $vat->date                      = date('Y-m-d H:i:s');
            $vat->note                      = __('parcel.delivered_success');
            $vat->save();

            $parcel->status = ParcelStatus::DELIVERED;
            $parcel->save();
            if ($request->send_sms_customer == 'on') {
                $msg = 'Dear Customer, Your parcel with ID ' . $parcel->tracking_id . ' is successfully delivered. To rate your experience visit:' . url('/') . '  -' . settings()->name;
                $response = app(SmsService::class)->sendSms($parcel->customer_phone, $msg);
            }
            if ($request->send_sms_merchant  == 'on') {
                $msg = 'Dear Merchant, your  parcel with ID ' . $parcel->tracking_id . ' is successfully delivered. Customer ' . $parcel->customer_name . ', ' . $parcel->customer_phone . ' Track here: ' . url('/') . ' -' . settings()->name;
                $response =  app(SmsService::class)->sendSms($parcel->merchant->user->mobile, $msg);
            }

            try {
                $msgNotification = 'Dear Merchant, your  parcel with ID ' . $parcel->tracking_id . ' is successfully delivered. Customer ' . $parcel->customer_name . ', ' . $parcel->customer_phone . ' Track here: ' . url('/') . ' -' . settings()->name;
                if (isset($parcel->merchant->user->web_token)) {
                    app(PushNotificationService::class)->sendStatusPushNotificationTo($parcel, $parcel->merchant->user->web_token, $msgNotification, 'merchant');
                }
                if (isset($parcel->merchant->user->device_token)) {
                    app(PushNotificationService::class)->sendStatusPushNotificationTo($parcel, $parcel->merchant->user->device_token, $msgNotification, 'merchant');
                }
            } catch (\Exception $exception) {
            }
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    private function storeDeliveryProofUpload($file): ?int
    {
        if (!$file) {
            return null;
        }

        $destinationPath = public_path('uploads/delivery_proofs');
        if (!is_dir($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }

        $fileName = Str::random(10) . date('YmdHis') . '.' . $file->getClientOriginalExtension();
        $file->move($destinationPath, $fileName);
        $relativePath = 'uploads/delivery_proofs/' . $fileName;

        $upload = new Upload();
        $upload->original = $relativePath;
        $upload->save();

        return $upload->id;
    }



    public function parcelDeliveredCancel($id, $request)
    {
        try {
            $parcel                                            = Parcel::find($id);
            if ($parcel->status == ParcelStatus::DELIVERED) {
                $pickupAsisgn = ParcelEvent::where(['parcel_id' => $id, 'parcel_status' => $parcel->status])->first();
                ParcelEvent::destroy($pickupAsisgn->id);
            }

            $reSceduleDeliveryman = ParcelEvent::where(['parcel_id' => $id, 'parcel_status' => ParcelStatus::DELIVERY_RE_SCHEDULE])->first();
            if ($reSceduleDeliveryman) {
                $deliveryManStatement                       = new DeliverymanStatement();
                $deliveryManStatement->parcel_id            = $id;
                $deliveryManStatement->delivery_man_id      = $reSceduleDeliveryman->deliveryMan->id;
                $deliveryManStatement->amount               = $reSceduleDeliveryman->deliveryMan->delivery_charge;
                $deliveryManStatement->type                 = StatementType::EXPENSE;
                $deliveryManStatement->date                 = date('Y-m-d H:i:s');
                $deliveryManStatement->note                 = __('statementNote.delivered_deliveryman_statement');
                $deliveryManStatement->save();

                //delivery man balance add
                if ($deliveryManStatement) {
                    $deliveryMan                        = DeliveryMan::find($reSceduleDeliveryman->deliveryMan->id);
                    $deliveryMan->current_balance       = $deliveryMan->current_balance - $deliveryManStatement->amount;
                    $deliveryMan->save();
                }

                $courierStatement                       = new CourierStatement();
                $courierStatement->parcel_id            = $id;
                $courierStatement->delivery_man_id      = $deliveryManStatement->delivery_man_id;
                $courierStatement->amount               = $deliveryManStatement->amount;
                $courierStatement->type                 = StatementType::INCOME;
                $courierStatement->date                 = date('Y-m-d H:i:s');
                $courierStatement->note                 = __('statementNote.delivered_deliveryman_statement');
                $courierStatement->save();

                //cash collection income from customer for store (- amount)
                $deliveryManStatement                       = new DeliverymanStatement();
                $deliveryManStatement->parcel_id            = $id;
                $deliveryManStatement->delivery_man_id      = $reSceduleDeliveryman->deliveryMan->id;
                $deliveryManStatement->amount               = ($parcel->cash_collection);
                $deliveryManStatement->type                 = StatementType::INCOME;
                $deliveryManStatement->cash_collection      = 1;
                $deliveryManStatement->date                 = date('Y-m-d H:i:s');
                $deliveryManStatement->note                 = __('statementNote.delivered_deliveryman_statement');
                $deliveryManStatement->save();

                //cash collection added  delivery man balance
                $deliveryManBalance                                = DeliveryMan::find($deliveryMan->id);
                $deliveryManBalance->current_balance               = $deliveryManBalance->current_balance +   $parcel->cash_collection;
                $deliveryManBalance->save();
            } else {

                $deliveryManAssign = ParcelEvent::where('parcel_id', $id)->where('parcel_status', ParcelStatus::DELIVERY_MAN_ASSIGN)->first();

                $deliveryManStatement                       = new DeliverymanStatement();
                $deliveryManStatement->parcel_id            = $id;
                $deliveryManStatement->delivery_man_id      = $deliveryManAssign->deliveryMan->id;
                $deliveryManStatement->amount               = $deliveryManAssign->deliveryMan->delivery_charge;
                $deliveryManStatement->type                 = StatementType::EXPENSE;
                $deliveryManStatement->date                 = date('Y-m-d H:i:s');
                $deliveryManStatement->note                 = __('statementNote.delivered_deliveryman_statement');
                $deliveryManStatement->save();

                //delivery man balance add
                if ($deliveryManStatement) {
                    $deliveryMan                            = DeliveryMan::find($deliveryManAssign->deliveryMan->id);
                    $deliveryMan->current_balance           = $deliveryMan->current_balance - $deliveryManStatement->amount;
                    $deliveryMan->save();
                }

                $courierStatement                       = new CourierStatement();
                $courierStatement->parcel_id            = $id;
                $courierStatement->delivery_man_id      = $deliveryManStatement->delivery_man_id;
                $courierStatement->amount               = $deliveryManStatement->amount;
                $courierStatement->type                 = StatementType::INCOME;
                $courierStatement->date                 = date('Y-m-d H:i:s');
                $courierStatement->note                 = __('statementNote.delivered_deliveryman_statement');
                $courierStatement->save();

                //cash collection income from customer for store (- amount)
                $deliveryManStatement                       = new DeliverymanStatement();
                $deliveryManStatement->parcel_id            = $id;
                $deliveryManStatement->delivery_man_id      = $deliveryManAssign->deliveryMan->id;
                $deliveryManStatement->amount               = ($parcel->cash_collection);
                $deliveryManStatement->type                 = StatementType::INCOME;
                $deliveryManStatement->cash_collection      = 1;
                $deliveryManStatement->date                 = date('Y-m-d H:i:s');
                $deliveryManStatement->note                 = __('statementNote.delivered_deliveryman_statement');
                $deliveryManStatement->save();
                //cash collection added  delivery man balance
                $deliveryManBalance                                = DeliveryMan::find($deliveryMan->id);
                $deliveryManBalance->current_balance               = $deliveryManBalance->current_balance +   $parcel->cash_collection;
                $deliveryManBalance->save();
            }


            //merchant statment
            $merchantStatement                   = new MerchantStatement();
            $merchantStatement->parcel_id        = $id;
            $merchantStatement->delivery_man_id  = $deliveryManStatement->delivery_man_id;
            $merchantStatement->amount           = $parcel->cash_collection;
            $merchantStatement->type             = StatementType::EXPENSE;
            $merchantStatement->date             =  date('Y-m-d H:i:s');
            $merchantStatement->note             = __('statementNote.delivered_merchant_statment');
            $merchantStatement->save();

            //merchant balance add
            if ($merchantStatement) {
                $merchant = Merchant::find($parcel->merchant_id);
                $merchant->current_balance = $merchant->current_balance - $parcel->cash_collection;
                $merchant->save();
            }

            //merchant expense vat + total charge amount
            //total delivery charge
            $merchantStatement                   = new MerchantStatement();
            $merchantStatement->parcel_id        = $id;
            $merchantStatement->delivery_man_id  = $deliveryManStatement->delivery_man_id;
            $merchantStatement->amount           = $parcel->total_delivery_amount;
            $merchantStatement->type             = StatementType::INCOME;
            $merchantStatement->date             =  date('Y-m-d H:i:s');
            $merchantStatement->note             = __('statementNote.delivered_merchant_statment');
            $merchantStatement->save();

            //vat
            $merchantStatement                   = new MerchantStatement();
            $merchantStatement->parcel_id        = $id;
            $merchantStatement->delivery_man_id  = $deliveryManStatement->delivery_man_id;
            $merchantStatement->amount           = $parcel->vat_amount;
            $merchantStatement->type             = StatementType::INCOME;
            $merchantStatement->date             =  date('Y-m-d H:i:s');
            $merchantStatement->note             = __('statementNote.delivered_merchant_statment');
            $merchantStatement->save();

            //vat and total charge minus from merchant current balance
            $deliveryCost = $parcel->total_delivery_amount + $parcel->vat_amount;
            $merchantCost = Merchant::find($parcel->merchant_id);
            $merchantCost->current_balance = $merchantCost->current_balance + $deliveryCost;
            $merchantCost->save();

            //end merchant expense vat + total charge amount


            //courier statement
            $courier_statement                  = new CourierStatement();
            $courier_statement->parcel_id       = $id;
            $courier_statement->delivery_man_id = $merchantStatement->delivery_man_id;
            $courier_statement->amount          = $parcel->total_delivery_amount;
            $courier_statement->type            = StatementType::EXPENSE;
            $courier_statement->date            = date('Y-m-d H:i:s');
            $courier_statement->note            = __('statementNote.delivered_merchant_courier_statement');
            $courier_statement->save();

            // Vat statement
            $vat                            = new VatStatement();
            $vat->parcel_id                 = $id;
            $vat->amount                    = $parcel->vat_amount;
            $vat->type                      = StatementType::EXPENSE;
            $vat->date                      = date('Y-m-d H:i:s');
            $vat->note                      = __('parcel.delivered_success');
            $vat->save();




            $dreschedule                        = ParcelEvent::where(['parcel_id' => $id, 'parcel_status' => ParcelStatus::DELIVERY_RE_SCHEDULE])->get();
            if (count($dreschedule) > 0) {
                $parcel->status = ParcelStatus::DELIVERY_RE_SCHEDULE;
            } else {
                $parcel->status = ParcelStatus::DELIVERY_MAN_ASSIGN;
            }
            $parcel->save();

            if (SmsSendSettingHelper(SmsSendStatus::DELIVERED_CANCEL_CUSTOMER)) {
                $msg = 'Dear ' . $parcel->customer_name . ', Your parcel with ID ' . $parcel->tracking_id . ' from ' . $parcel->merchant->business_name . 'will be cancel. Track here: ' . url('/') . ' -' . settings()->name;
                $response = app(SmsService::class)->sendSms($parcel->customer_phone, $msg);
            }

            if (SmsSendSettingHelper(SmsSendStatus::DELIVERED_CANCEL_MERCHANT)) {
                $msg = 'Dear ' . $parcel->merchant->business_name . ', your  parcel with ID ' . $parcel->tracking_id . '  Delivered cancel. Customer ' . $parcel->customer_name . ', ' . $parcel->customer_phone . ' Track here: ' . url('/') . ' -' . settings()->name;
                $response =  app(SmsService::class)->sendSms($parcel->merchant->user->mobile, $msg);
            }

            try {
                $msgNotification = 'Dear ' . $parcel->merchant->business_name . ', your  parcel with ID ' . $parcel->tracking_id . '  Delivered cancel. Customer ' . $parcel->customer_name . ', ' . $parcel->customer_phone . ' Track here: ' . url('/') . ' -' . settings()->name;
                if (isset($parcel->merchant->user->web_token)) {
                    app(PushNotificationService::class)->sendStatusPushNotificationTo($parcel, $parcel->merchant->user->web_token, $msgNotification, 'merchant');
                }
                if (isset($parcel->merchant->user->device_token)) {
                    app(PushNotificationService::class)->sendStatusPushNotificationTo($parcel, $parcel->merchant->user->device_token, $msgNotification, 'merchant');
                }
            } catch (\Exception $exception) {
                // dd($exception);
            }
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }


    public function parcelPartialDelivered($id, $request)
    {
        try {
            // Parcel Event
            $parcelPartialDelivered                     = new ParcelEvent();
            $parcelPartialDelivered->parcel_id          = $id;
            $parcelPartialDelivered->note               = $request->note;
            $parcelPartialDelivered->parcel_status      = ParcelStatus::PARTIAL_DELIVERED;
            $parcelPartialDelivered->created_by         = Auth::user()->id;
            $parcelPartialDelivered->save();
            $parcel                           = Parcel::find($id);
            // Parcel
            $parcel->status                             = ParcelStatus::PARTIAL_DELIVERED;
            $parcel->current_payable                    = $request->cash_collection - $chargeWithVat;
            $parcel->partial_delivered                  = BooleanStatus::YES;
            $parcel->save();

            if ($parcel) {
                //delivery charge
                $reSceduleDeliveryman            = ParcelEvent::Where('parcel_id', $id)->where('parcel_status', ParcelStatus::DELIVERY_RE_SCHEDULE)->first();
                if ($reSceduleDeliveryman) {
                    $deliveryManStatement                       = new DeliverymanStatement();
                    $deliveryManStatement->parcel_id            = $id;
                    $deliveryManStatement->delivery_man_id      = $reSceduleDeliveryman->deliveryMan->id;
                    $deliveryManStatement->amount               = $reSceduleDeliveryman->deliveryMan->delivery_charge;
                    $deliveryManStatement->type                 = StatementType::INCOME;
                    $deliveryManStatement->cash_collection      = 1;
                    $deliveryManStatement->date                 = date('Y-m-d H:i:s');
                    $deliveryManStatement->note                 = __('statementNote.partial_delivered_deliveryman_statement');
                    $deliveryManStatement->save();

                    //delivery man balance add
                    if ($deliveryManStatement) {
                        $deliveryMan                            = DeliveryMan::find($reSceduleDeliveryman->deliveryMan->id);
                        $deliveryMan->current_balance           = $deliveryMan->current_balance + $deliveryManStatement->amount;
                        $deliveryMan->save();
                    }

                    $courierStatement                       = new CourierStatement();
                    $courierStatement->parcel_id            = $id;
                    $courierStatement->delivery_man_id      = $deliveryManStatement->delivery_man_id;
                    $courierStatement->amount               = $deliveryManStatement->amount;
                    $courierStatement->type                 = StatementType::EXPENSE;
                    $courierStatement->date                 = date('Y-m-d H:i:s');
                    $courierStatement->note                 = __('statementNote.delivered_deliveryman_statement');
                    $courierStatement->save();


                    //cash collection income from customer for store (- amount)
                    $deliveryManStatement                       = new DeliverymanStatement();
                    $deliveryManStatement->parcel_id            = $id;
                    $deliveryManStatement->delivery_man_id      = $reSceduleDeliveryman->deliveryMan->id;
                    $deliveryManStatement->amount               = ($parcel->cash_collection);
                    $deliveryManStatement->cash_collection      = 1;
                    $deliveryManStatement->type                 = StatementType::EXPENSE;
                    $deliveryManStatement->date                 = date('Y-m-d H:i:s');
                    $deliveryManStatement->note                 = __('statementNote.delivered_deliveryman_statement');
                    $deliveryManStatement->save();

                    //cash collection added  delivery man balance
                    $deliveryManBalance                                = DeliveryMan::find($deliveryMan->id);
                    $deliveryManBalance->current_balance               = $deliveryManBalance->current_balance + (-$parcel->cash_collection);
                    $deliveryManBalance->save();
                } else {
                    $deliveryManAssign = ParcelEvent::where('parcel_id', $id)->where('parcel_status', ParcelStatus::DELIVERY_MAN_ASSIGN)->first();
                    $deliveryManStatement                       = new DeliverymanStatement();
                    $deliveryManStatement->parcel_id            = $id;
                    $deliveryManStatement->delivery_man_id      = $deliveryManAssign->deliveryMan->id;
                    $deliveryManStatement->amount               = $deliveryManAssign->deliveryMan->delivery_charge;
                    $deliveryManStatement->type                 = StatementType::INCOME;
                    $deliveryManStatement->cash_collection      = 1;
                    $deliveryManStatement->date                 = date('Y-m-d H:i:s');
                    $deliveryManStatement->note                 = __('statementNote.partial_delivered_deliveryman_statement');
                    $deliveryManStatement->save();

                    //delivery man balance add
                    if ($deliveryManStatement) {
                        $deliveryMan                            = DeliveryMan::find($deliveryManAssign->deliveryMan->id);
                        $deliveryMan->current_balance           = $deliveryMan->current_balance + $deliveryManStatement->amount;
                        $deliveryMan->save();
                    }

                    $courierStatement                       = new CourierStatement();
                    $courierStatement->parcel_id            = $id;
                    $courierStatement->delivery_man_id      = $deliveryManStatement->delivery_man_id;
                    $courierStatement->amount               = $deliveryManStatement->amount;
                    $courierStatement->type                 = StatementType::EXPENSE;
                    $courierStatement->date                 = date('Y-m-d H:i:s');
                    $courierStatement->note                 = __('statementNote.delivered_deliveryman_statement');
                    $courierStatement->save();

                    //cash collection income from customer for store (- amount)
                    $deliveryManStatement                       = new DeliverymanStatement();
                    $deliveryManStatement->parcel_id            = $id;
                    $deliveryManStatement->delivery_man_id      = $deliveryManAssign->deliveryMan->id;
                    $deliveryManStatement->amount               = ($parcel->cash_collection);
                    $deliveryManStatement->cash_collection      = 1;
                    $deliveryManStatement->type                 = StatementType::EXPENSE;
                    $deliveryManStatement->date                 = date('Y-m-d H:i:s');
                    $deliveryManStatement->note                 = __('statementNote.delivered_deliveryman_statement');
                    $deliveryManStatement->save();

                    //cash collection added  delivery man balance
                    $deliveryManBalance                                = DeliveryMan::find($deliveryMan->id);
                    $deliveryManBalance->current_balance               = $deliveryManBalance->current_balance + (-$parcel->cash_collection);
                    $deliveryManBalance->save();
                }

                //merchant statment
                $merchantStatement                   = new MerchantStatement();
                $merchantStatement->parcel_id        = $id;
                $merchantStatement->delivery_man_id  = $deliveryManStatement->delivery_man_id;
                $merchantStatement->amount           = $parcel->cash_collection;
                $merchantStatement->type             = StatementType::INCOME;
                $merchantStatement->date             =  date('Y-m-d H:i:s');
                $merchantStatement->note             = __('statementNote.partial_delivered_merchant_statment');
                $merchantStatement->save();

                //merchant balance add
                if ($merchantStatement) {
                    $merchant = Merchant::find($parcel->merchant_id);
                    $merchant->current_balance = $merchant->current_balance + $parcel->cash_collection;
                    $merchant->save();
                }

                //merchant expense vat + total charge amount

                //total delivery charge
                $merchantStatement                   = new MerchantStatement();
                $merchantStatement->parcel_id        = $id;
                $merchantStatement->delivery_man_id  = $deliveryManStatement->delivery_man_id;
                $merchantStatement->amount           = $parcel->total_delivery_amount;
                $merchantStatement->type             = StatementType::EXPENSE;
                $merchantStatement->date             =  date('Y-m-d H:i:s');
                $merchantStatement->note             = __('statementNote.delivered_merchant_statment');
                $merchantStatement->save();

                //vat
                $merchantStatement                   = new MerchantStatement();
                $merchantStatement->parcel_id        = $id;
                $merchantStatement->delivery_man_id  = $deliveryManStatement->delivery_man_id;
                $merchantStatement->amount           = $parcel->vat_amount;
                $merchantStatement->type             = StatementType::EXPENSE;
                $merchantStatement->date             =  date('Y-m-d H:i:s');
                $merchantStatement->note             = __('statementNote.delivered_merchant_statment');
                $merchantStatement->save();

                //vat and total charge minus from merchant current balance
                $deliveryCost = $parcel->total_delivery_amount + $parcel->vat_amount;
                $merchantCost = Merchant::find($parcel->merchant_id);
                $merchantCost->current_balance = $merchantCost->current_balance - $deliveryCost;
                $merchantCost->save();

                //end merchant expense vat + total charge amount

                $courier_statement                  = new CourierStatement();
                $courier_statement->parcel_id       = $id;
                $courier_statement->delivery_man_id = $merchantStatement->delivery_man_id;
                $courier_statement->amount          = $parcel->total_delivery_amount;
                $courier_statement->type            = StatementType::INCOME;
                $courier_statement->date            = date('Y-m-d H:i:s');
                $courier_statement->note            = __('statementNote.partial_delivered_merchant_courier_statement');
                $courier_statement->save();


                // Vat statement
                $vat                                        = new VatStatement();
                $vat->parcel_id                             = $id;
                $vat->amount                                = $parcel->vat_amount;
                $vat->type                                  = StatementType::INCOME;
                $vat->date                                  = date('Y-m-d H:i:s');
                $vat->note                                  = __('parcel.partial_delivered_success');
                $vat->save();
            }

            if ($request->send_sms_customer == 'on') {
                $msg = 'Dear ' . $parcel->customer_name . ', Your parcel with ID ' . $parcel->tracking_id . '  is Partials Delivered please giving amount(' . $parcel->cash_collection . ') by  To rate your experience visit:' . url('/') . '  -' . settings()->name;
                $response = app(SmsService::class)->sendSms($parcel->customer_phone, $msg);
            }
            if ($request->send_sms_merchant  == 'on') {
                $msg = 'Dear ' . $parcel->merchant->business_name . ', your  parcel with ID ' . $parcel->tracking_id . ' is Partials Delivered. Customer ' . $parcel->customer_name . ', ' . $parcel->customer_phone . ' taking amount(' . $parcel->cash_collection . ')  Track here: ' . url('/') . ' -' . settings()->name;
                $response =  app(SmsService::class)->sendSms($parcel->merchant->user->mobile, $msg);
            }
            try {
                $msgNotification = 'Dear ' . $parcel->merchant->business_name . ', your  parcel with ID ' . $parcel->tracking_id . ' is Partials Delivered. Customer ' . $parcel->customer_name . ', ' . $parcel->customer_phone . ' taking amount(' . $parcel->cash_collection . ')  Track here: ' . url('/') . ' -' . settings()->name;
                if (isset($parcel->merchant->user->web_token)) {
                    app(PushNotificationService::class)->sendStatusPushNotificationTo($parcel, $parcel->merchant->user->web_token, $msgNotification, 'merchant');
                }
                if (isset($parcel->merchant->user->device_token)) {
                    app(PushNotificationService::class)->sendStatusPushNotificationTo($parcel, $parcel->merchant->user->device_token, $msgNotification, 'merchant');
                }
            } catch (\Exception $exception) {
                dd($exception);
            }
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }


    public function parcelPartialDeliveredCancel($id, $request)
    {
        try {
            $parcel                             = Parcel::find($id);
            // keep existing delivery totals; no COD adjustments
            $current_vat                    = $parcel->vat_amount;
            // Vat statement
            $vat                                           = new VatStatement();
            $vat->parcel_id                                = $id;
            $vat->amount                                   = $current_vat;
            $vat->type                                     = StatementType::EXPENSE;
            $vat->date                                     = date('Y-m-d H:i:s');
            $vat->note                                     = __('parcel.partial_delivered_cancel');
            $vat->save();

            if ($parcel->status == ParcelStatus::PARTIAL_DELIVERED) {
                $pickupAsisgn  = ParcelEvent::where(['parcel_id' => $id, 'parcel_status' => $parcel->status])->delete();
            }
            //statements
            $deliveryReschedule = ParcelEvent::where(['parcel_id' => $id, 'parcel_status' => ParcelStatus::DELIVERY_RE_SCHEDULE])->first();
            if ($deliveryReschedule) {

                $deliveryManStatement                       = new DeliverymanStatement();
                $deliveryManStatement->parcel_id            = $id;
                $deliveryManStatement->delivery_man_id      = $deliveryReschedule->deliveryMan->id;
                $deliveryManStatement->amount               = $deliveryReschedule->deliveryMan->delivery_charge;
                $deliveryManStatement->type                 = StatementType::EXPENSE;
                $deliveryManStatement->cash_collection      = 1;
                $deliveryManStatement->date                 = date('Y-m-d H:i:s');
                $deliveryManStatement->note                 = __('statementNote.partial_delivered_deliveryman_statement');
                $deliveryManStatement->save();

                //delivery man balance add
                if ($deliveryManStatement) {
                    $deliveryMan                            = DeliveryMan::find($deliveryReschedule->deliveryMan->id);
                    $deliveryMan->current_balance           = $deliveryMan->current_balance - $deliveryManStatement->amount;
                    $deliveryMan->save();
                }

                $courierStatement                       = new CourierStatement();
                $courierStatement->parcel_id            = $id;
                $courierStatement->delivery_man_id      = $deliveryManStatement->delivery_man_id;
                $courierStatement->amount               = $deliveryManStatement->amount;
                $courierStatement->type                 = StatementType::INCOME;
                $courierStatement->date                 = date('Y-m-d H:i:s');
                $courierStatement->note                 = __('statementNote.delivered_deliveryman_statement');
                $courierStatement->save();

                //cash collection income from customer for store (- amount)
                $deliveryManStatement                       = new DeliverymanStatement();
                $deliveryManStatement->parcel_id            = $id;
                $deliveryManStatement->delivery_man_id      = $deliveryReschedule->deliveryMan->id;
                $deliveryManStatement->amount               = ($old_cash_collection);
                $deliveryManStatement->cash_collection      = 1;
                $deliveryManStatement->type                 = StatementType::INCOME;
                $deliveryManStatement->date                 = date('Y-m-d H:i:s');
                $deliveryManStatement->note                 = __('statementNote.delivered_deliveryman_statement');
                $deliveryManStatement->save();

                //cash collection added  delivery man balance
                $deliveryManBalance                                = DeliveryMan::find($deliveryMan->id);
                $deliveryManBalance->current_balance               = $deliveryManBalance->current_balance + $old_cash_collection;
                $deliveryManBalance->save();
            } else {

                $deliveryManAssign                          = ParcelEvent::where(['parcel_id' => $id, 'parcel_status' => ParcelStatus::DELIVERY_MAN_ASSIGN])->first();
                $deliveryManStatement                       = new DeliverymanStatement();
                $deliveryManStatement->parcel_id            = $id;
                $deliveryManStatement->delivery_man_id      = $deliveryManAssign->deliveryMan->id;
                $deliveryManStatement->amount               = $deliveryManAssign->deliveryMan->delivery_charge;
                $deliveryManStatement->type                 = StatementType::EXPENSE;
                $deliveryManStatement->date                 = date('Y-m-d H:i:s');
                $deliveryManStatement->note                 = __('statementNote.partial_delivered_deliveryman_statement');
                $deliveryManStatement->save();

                //delivery man balance add
                if ($deliveryManStatement) {
                    $deliveryMan                            = DeliveryMan::find($deliveryManAssign->deliveryMan->id);
                    $deliveryMan->current_balance           = $deliveryMan->current_balance - $deliveryManStatement->amount;
                    $deliveryMan->save();
                }

                $courierStatement                       = new CourierStatement();
                $courierStatement->parcel_id            = $id;
                $courierStatement->delivery_man_id      = $deliveryManStatement->delivery_man_id;
                $courierStatement->amount               = $deliveryManStatement->amount;
                $courierStatement->type                 = StatementType::INCOME;
                $courierStatement->date                 = date('Y-m-d H:i:s');
                $courierStatement->note                 = __('statementNote.delivered_deliveryman_statement');
                $courierStatement->save();

                //cash collection income from customer for store (- amount)
                $deliveryManStatement                       = new DeliverymanStatement();
                $deliveryManStatement->parcel_id            = $id;
                $deliveryManStatement->delivery_man_id      = $deliveryManAssign->deliveryMan->id;
                $deliveryManStatement->amount               = ($old_cash_collection);
                $deliveryManStatement->cash_collection      = 1;
                $deliveryManStatement->type                 = StatementType::INCOME;
                $deliveryManStatement->date                 = date('Y-m-d H:i:s');
                $deliveryManStatement->note                 = __('statementNote.delivered_deliveryman_statement');
                $deliveryManStatement->save();

                //cash collection added  delivery man balance
                $deliveryManBalance                                = DeliveryMan::find($deliveryMan->id);
                $deliveryManBalance->current_balance               = $deliveryManBalance->current_balance + $old_cash_collection;
                $deliveryManBalance->save();
            }

            //merchant statment
            $merchantStatement                   = new MerchantStatement();
            $merchantStatement->parcel_id        = $id;
            $merchantStatement->delivery_man_id  = $deliveryManStatement->delivery_man_id;
            $merchantStatement->amount           = $old_cash_collection;
            $merchantStatement->type             = StatementType::EXPENSE;
            $merchantStatement->date             =  date('Y-m-d H:i:s');
            $merchantStatement->note             = __('statementNote.partial_delivered_merchant_statment');
            $merchantStatement->save();

            //merchant balance add
            if ($merchantStatement) {
                $merchant = Merchant::find($parcel->merchant_id);
                $merchant->current_balance = $merchant->current_balance - $old_cash_collection;
                $merchant->save();
            }

            //merchant expense vat + total charge amount
            //total delivery charge
            $merchantStatement                   = new MerchantStatement();
            $merchantStatement->parcel_id        = $id;
            $merchantStatement->delivery_man_id  = $deliveryManStatement->delivery_man_id;
            $merchantStatement->amount           = $old_total_delivery_amount;
            $merchantStatement->type             = StatementType::INCOME;
            $merchantStatement->date             =  date('Y-m-d H:i:s');
            $merchantStatement->note             = __('statementNote.delivered_merchant_statment');
            $merchantStatement->save();
            //vat
            $merchantStatement                   = new MerchantStatement();
            $merchantStatement->parcel_id        = $id;
            $merchantStatement->delivery_man_id  = $deliveryManStatement->delivery_man_id;
            $merchantStatement->amount           = $old_vat_amount;
            $merchantStatement->type             = StatementType::INCOME;
            $merchantStatement->date             =  date('Y-m-d H:i:s');
            $merchantStatement->note             = __('statementNote.delivered_merchant_statment');
            $merchantStatement->save();
            //vat and total charge plus from merchant current balance
            $deliveryCost = $old_total_delivery_amount + $parcel->vat_amount;
            $merchantCost = Merchant::find($parcel->merchant_id);
            $merchantCost->current_balance = $merchantCost->current_balance + $deliveryCost;
            $merchantCost->save();

            //end merchant expense vat + total charge amount
            $courier_statement                  = new CourierStatement();
            $courier_statement->parcel_id       = $id;
            $courier_statement->delivery_man_id = $merchantStatement->delivery_man_id;
            $courier_statement->amount          = $old_total_delivery_amount;
            $courier_statement->type            = StatementType::EXPENSE;
            $courier_statement->date            = date('Y-m-d H:i:s');
            $courier_statement->note            = __('statementNote.partial_delivered_merchant_courier_statement_cancel');
            $courier_statement->save();
            //end statements
            $dreschedule                            = ParcelEvent::where(['parcel_id' => $id, 'parcel_status' => ParcelStatus::DELIVERY_RE_SCHEDULE])->first();
            if ($dreschedule !== null) {
                $parcel->status                     = ParcelStatus::DELIVERY_RE_SCHEDULE;
            } else {
                $parcel->status                     = ParcelStatus::DELIVERY_MAN_ASSIGN;
            }
            $parcel->partial_delivered              = BooleanStatus::NO;
            $parcel->save();
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function pickupdatemanAssignedCancel($id, $request)
    {
        try {
            $parcel = Parcel::find($id);
            if ($parcel->status == ParcelStatus::PICKUP_ASSIGN) {
                $pickupAsisgn = ParcelEvent::where(['parcel_id' => $id, 'parcel_status' => $parcel->status])->first();
                ParcelEvent::destroy($pickupAsisgn->id);
            }
            $parcel->status   = ParcelStatus::PENDING;
            $parcel->save();
            return true;
        } catch (\Throwable $th) {

            return false;
        }
    }

    public function PickupReScheduleCancel($id, $request)
    {
        try {
            $parcel = Parcel::find($id);
            if ($parcel->status == ParcelStatus::PICKUP_RE_SCHEDULE) {
                ParcelEvent::where(['parcel_id' => $id, 'parcel_status' => $parcel->status])->delete();
            }
            $parcel->status       = ParcelStatus::PICKUP_ASSIGN;
            $parcel->save();
            return true;
        } catch (\Throwable $th) {

            return false;
        }
    }

    public function receivedBypickupmanCancel($id, $request)
    {
        try {
            $parcel = Parcel::find($id);
            if ($parcel->status == ParcelStatus::RECEIVED_BY_PICKUP_MAN) {
                $pickupAsisgn = ParcelEvent::where(['parcel_id' => $id, 'parcel_status' => $parcel->status])->first();
                ParcelEvent::destroy($pickupAsisgn->id);
            }
            $parcel->status    = ParcelStatus::PICKUP_ASSIGN;
            $parcel->save();
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function deliverymanAssignCancel($id, $request)
    {
        try {
            $parcel = Parcel::find($id);
            $parcel->status   = ParcelStatus::DELIVERY_MAN_ASSIGN_CANCEL;
            $parcel->delivery_man_id   = null;
            $parcel->save();
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }
    public function deliveryReScheduleCancel($id, $request)
    {
        try {
            $parcel = Parcel::find($id);
            $parcel->status   = ParcelStatus::DELIVERY_RE_SCHEDULE_CANCEL;
            $parcel->delivery_man_id   = null;
            $parcel->save();
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }


    public function search($data)
    {
        $result = Parcel::with('merchant', 'merchant.user')->where('tracking_id', $data['search'])->where('status', ParcelStatus::RECEIVED_WAREHOUSE)->first();
        if ($result == null) {
            $result = Parcel::with('merchant', 'merchant.user')->where('tracking_id', $data['search'])->where('status', ParcelStatus::RECEIVED_BY_HUB)->first();
        }
        if ($result != null) {
            return $result;
        } else {
            return 0;
        }
    }

    public function searchDeliveryManAssingMultipleParcel($data)
    {
        $result = Parcel::with('merchant', 'merchant.user')->where('tracking_id', $data['search'])->where('status', ParcelStatus::RECEIVED_WAREHOUSE)->first();
        if ($result == null) {
            $result = Parcel::with('merchant', 'merchant.user')->where('tracking_id', $data['search'])->where('status', ParcelStatus::RECEIVED_BY_HUB)->first();
        }
        if ($result != null) {
            return $result;
        } else {
            return 0;
        }
    }

    public function searchExpense($data)
    {
        $result = Parcel::with('merchant', 'merchant.user')->where('tracking_id', $data['search'])->first();
        if ($result != null) {
            return $result;
        } else {
            return 0;
        }
    }

    public function searchIncome($data)
    {
        $result = Parcel::with('merchant', 'merchant.user')->where('tracking_id', $data['search'])->first();
        if ($result != null) {
            return $result;
        } else {
            return 0;
        }
    }


    public function pickupdatemanAssignedBulk($request)
    {
        try {
            foreach ($request->parcel_id as  $id) {
                $pickupAsisgn                = new ParcelEvent();
                $pickupAsisgn->parcel_id     = $id;
                $pickupAsisgn->pickup_man_id = $request->delivery_man_id;
                $pickupAsisgn->note          = $request->note;
                $pickupAsisgn->parcel_status = ParcelStatus::PICKUP_ASSIGN;
                $pickupAsisgn->created_by    = Auth::user()->id;
                $pickupAsisgn->save();
                $parcel                      = Parcel::find($id);
                $parcel->status              = ParcelStatus::PICKUP_ASSIGN;
                $parcel->save();
                if ($request->send_sms_pickuman == 'on') {
                    $msg = 'Dear ' . $pickupAsisgn->pickupman->user->name . ', Please pickup parcel with ID ' . $parcel->tracking_id . ' parcel from (' . $parcel->merchant->business_name . ',' . $parcel->merchant->user->mobile . ',' . $parcel->merchant->address . ') within ' . dateFormat($parcel->pickup_date) . ' -' . settings()->name;
                    $response =  app(SmsService::class)->sendSms($pickupAsisgn->pickupman->user->mobile, $msg);
                }
                try {
                    $msgNotification = 'Dear ' . $pickupAsisgn->pickupman->user->name . ', Please pickup parcel with ID ' . $parcel->tracking_id . ' parcel from (' . $parcel->merchant->business_name . ',' . $parcel->merchant->user->mobile . ',' . $parcel->merchant->address . ') within ' . dateFormat($parcel->pickup_date) . ' -' . settings()->name;
                    app(PushNotificationService::class)->sendStatusPushNotificationTo($parcel, $pickupAsisgn->pickupman->user->device_token, $msgNotification, 'deliveryMan');
                } catch (\Exception $exception) {
                }
                if ($request->send_sms_merchant  == 'on') {
                    $msg = 'Dear ' . $parcel->merchant->business_name . ', your  parcel with ID ' . $parcel->tracking_id . ' Pickup man assign from ' . settings()->name . '. Assign by' . $pickupAsisgn->pickupman->user->name . ', ' . $pickupAsisgn->pickupman->user->mobile . ' Track here: ' . url('/') . ' -' . settings()->name;
                    $response =  app(SmsService::class)->sendSms($parcel->merchant->user->mobile, $msg);
                }
            }
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function AssignReturnToMerchantBulk($request)
    {
        try {
            foreach ($request->parcel_id as $id) {
                DB::beginTransaction();
                $returnassigntomerchant                  = new ParcelEvent();
                $returnassigntomerchant->parcel_id       = $id;
                $returnassigntomerchant->delivery_man_id = $request->delivery_man_id;
                $returnassigntomerchant->note            = $request->note;
                $returnassigntomerchant->parcel_status   = ParcelStatus::RETURN_ASSIGN_TO_MERCHANT;
                $returnassigntomerchant->created_by      = Auth::user()->id;
                $returnassigntomerchant->save();
                // Delivery man current balance update
                $deliveryMan                                = DeliveryMan::find($request->delivery_man_id);
                $deliveryMan->current_balance               = $deliveryMan->current_balance + $deliveryMan->return_charge;
                $deliveryMan->save();
                // Courier statement
                $deliveryManStatement                       = new DeliverymanStatement();
                $deliveryManStatement->parcel_id            = $id;
                $deliveryManStatement->delivery_man_id      = $request->delivery_man_id;
                $deliveryManStatement->amount               = $deliveryMan->return_charge;
                $deliveryManStatement->type                 = StatementType::INCOME;
                $deliveryManStatement->date                 = date('Y-m-d H:i:s');
                $deliveryManStatement->note                 = __('statementNote.returned_to_merchant_income');
                $deliveryManStatement->save();
                // Courier statement
                $courierStatement                           = new CourierStatement();
                $courierStatement->parcel_id                = $id;
                $courierStatement->delivery_man_id          = $request->delivery_man_id;
                $courierStatement->amount                   = $deliveryMan->return_charge;
                $courierStatement->type                     = StatementType::EXPENSE;
                $courierStatement->date                     = date('Y-m-d H:i:s');
                $courierStatement->note                     = __('statementNote.returned_to_merchant_expense');
                $courierStatement->save();
                // End
                $parcel                = Parcel::find($id);
                $parcel->delivery_date = $request->date;
                $parcel->status        = ParcelStatus::RETURN_ASSIGN_TO_MERCHANT;
                $parcel->save();
                DB::commit();
                if ($request->send_sms == 'on') {
                    $msg = 'Dear ' . $parcel->merchant->business_name . ', parcel with ID ' . $parcel->tracking_id . ' is return to you by ' . $returnassigntomerchant->deliveryMan->user->name . ', ' . $returnassigntomerchant->deliveryMan->user->mobile . '. visit:' . url('/') . '  -' . settings()->name;
                    $response =  app(SmsService::class)->sendSms($parcel->merchant->user->mobile, $msg);
                }
            }
            return true;
        } catch (\Throwable $th) {
            DB::rollBack();
            return false;
        }
    }

    public function bulkParcels($ids)
    {
        return Parcel::whereIn('id', $ids)->get();
    }

    //app dashboard
    public function deliverymanStatusParcel($request, $status, $deliverymanID)
    {
        if ($status == ParcelStatus::DELIVERED) :
            return Parcel::orderByDesc('id')->with('merchant', 'parcelEvent')->whereIn('status', [ParcelStatus::DELIVERED, ParcelStatus::PARTIAL_DELIVERED])->where(function ($query)  use ($deliverymanID) {
                $query->wherehas('parcelEvent', function ($eventquery) use ($deliverymanID) {
                    $eventquery->where('delivery_man_id', $deliverymanID);
                });
            })->paginate($request->limit);
        elseif ($status == ParcelStatus::RETURN_TO_COURIER):
            return Parcel::orderByDesc('id')->with('merchant', 'parcelEvent')->whereIn('status', [$status])->where(function ($query)  use ($deliverymanID) {
                $query->wherehas('parcelEvent', function ($eventquery)  use ($deliverymanID) {
                    $eventquery->where('delivery_man_id', $deliverymanID);
                });
            })->paginate($request->limit);
        elseif ($status == ParcelStatus::DELIVERY_MAN_ASSIGN):
            return Parcel::orderByDesc('id')->with('merchant', 'parcelEvent')->whereIn('status', [ParcelStatus::DELIVERY_MAN_ASSIGN, ParcelStatus::DELIVERY_RE_SCHEDULE])->where(function ($query)  use ($status, $deliverymanID) {
                $query->wherehas('parcelEvent', function ($eventquery)  use ($status, $deliverymanID) {
                    $eventquery->where('delivery_man_id', $deliverymanID);
                });
            })->paginate($request->limit);

        elseif ($status == ParcelStatus::PICKUP_ASSIGN):
            return Parcel::orderByDesc('id')->with('merchant', 'parcelEvent')->whereIn('status', [$status, ParcelStatus::PICKUP_RE_SCHEDULE])->where(function ($query)  use ($deliverymanID) {
                $query->wherehas('parcelEvent', function ($eventquery)  use ($deliverymanID) {
                    $eventquery->where('pickup_man_id', $deliverymanID);
                });
            })->paginate($request->limit);
        else :
            return Parcel::orderByDesc('id')->with('merchant', 'parcelEvent')->where('status', $status)->where(function ($query)  use ($deliverymanID) {
                $query->wherehas('parcelEvent', function ($eventquery)  use ($deliverymanID) {
                    $eventquery->where('delivery_man_id', $deliverymanID);
                });
            })->paginate($request->limit);
        endif;
    }

    public function searchOrder($request, $deliverymanID)
    {
        return Parcel::orderByDesc('id')
            ->with(['merchant', 'parcelEvent'])
            ->where('delivery_man_id', $deliverymanID)
            ->where(function ($query) use ($request) {
                if ($request->search != '' && $request->search != '"""'):
                    $query->where('tracking_id', 'Like', '%' . $request->search . '%')
                        ->orWhere('customer_last_name', 'Like', '%' . $request->search . '%')
                        ->orWhere('customer_phone', 'Like', '%' . $request->search . '%')
                        ->orWhere('receiver_email', 'Like', '%' . $request->search . '%')
                        ->orWhere('customer_address', 'Like', '%' . $request->search . '%')
                        ->orWhere('customer_first_name', 'Like', '%' . $request->search . '%')
                        ->orWhere('sender_first_name', 'Like', '%' . $request->search . '%')
                        ->orWhere('sender_company_name', 'Like', '%' . $request->search . '%')
                        ->orWhere('sender_phone', 'Like', '%' . $request->search . '%')
                        ->orWhereHas('merchant', function ($query) use ($request) {
                            $query->where('business_name', 'Like', '%' . $request->search . '%');
                        });
                endif;
            })

            ->paginate($request->limit);
    }
    public function orderDeliverymanAssign($request, $deliverymanID)
    {
        $order = Parcel::where('delivery_man_id', $deliverymanID)->where('tracking_id', $request->tracking_id)->first();
        if ($order) {
            $data['success'] = false;
            $data['message'] = 'Already assigned to deliveryMan for order';
        } else {
            $order = Parcel::where('tracking_id', $request->tracking_id)->first();
            if ($order) {
                $this->deliverymanAssign($order->id, $request);
                $data['success'] = true;
                $data['message'] = 'Successfully assigned to deliveryMan for order';
            } else {
                $data['message'] = 'Data Not Found. Please try again';
            }
        }
        return $data;
    }
    public function deliverymanAssignedParcel($request, $deliverymanID)
    {
        return Parcel::orderByDesc('id')
            ->with(['merchant', 'parcelEvent'])
            ->where('delivery_man_id', $deliverymanID)
            ->whereNotIn('status', [
                ParcelStatus::DELIVERED,
                ParcelStatus::PARTIAL_DELIVERED,
                ParcelStatus::DELIVERY_FAILED,
                ParcelStatus::DELIVERY_FAILURE
            ])
            ->paginate($request->limit);
    }

    public function deliverymanDeliveredParcel($request, $deliverymanID)
    {
        return Parcel::orderByDesc('id')
            ->with(['merchant', 'parcelEvent'])
            ->where('delivery_man_id', $deliverymanID)
            ->whereIn('status', [
                ParcelStatus::DELIVERED,
                ParcelStatus::PARTIAL_DELIVERED,
            ])
            ->paginate($request->limit);
    }
    public function deliverymanFailuredParcel($request, $deliverymanID)
    {
        return Parcel::orderByDesc('id')
            ->with(['merchant', 'parcelEvent'])
            ->where('delivery_man_id', $deliverymanID)
            ->whereIn('status', [
                ParcelStatus::DELIVERY_FAILURE,
                ParcelStatus::DELIVERY_FAILED,
                ParcelStatus::PARCEL_CANCEL,
                ParcelStatus::DELIVERY_ATTEMPT1,
                ParcelStatus::DELIVERY_ATTEMPT2,
                ParcelStatus::DELIVERY_ATTEMPT3,
                ParcelStatus::DELIVERY_MAN_ASSIGN1,
                ParcelStatus::RETURN_TO_COURIER,
                ParcelStatus::RETURNING,
                ParcelStatus::TRANSIT_SENDING_PROVINCE,
                ParcelStatus::ON_THE_WAY_SENDING_PROVINCE,
                ParcelStatus::ARRIVED_TO_SENDING_HUB,
                ParcelStatus::RETURN_ASSIGN_TO_MERCHANT,
                ParcelStatus::RETURN_ASSIGN_TO_MERCHANT1,
                ParcelStatus::RETURN_MERCHANT_RE_SCHEDULE,
                ParcelStatus::RETURN_RECEIVED_BY_MERCHANT,
            ])
            ->paginate($request->limit);
    }

    //end app dashboard
    public function ParcelSearch($request)
    {
        return  Parcel::where('customer_name', 'Like', '%' . $request->search . '%')
            ->orWhere('customer_phone', 'Like', '%' . $request->search . '%')
            ->orWhere('customer_address', 'Like', '%' . $request->search . '%')
            ->orWhere('invoice_no', 'Like', '%' . $request->search . '%')
            ->orWhere('tracking_id', 'Like', '%' . $request->search . '%')
            ->orWhereHas('merchant', function ($query) use ($request) {
                $query->where('business_name', 'Like', '%' . $request->search . '%');
            })
            ->paginate(10);
    }

    public function parcelMultiplePrintLabel($request)
    {
        return Parcel::whereIn('id', $request->parcels)->with('merchant', 'merchant.user', 'merchantShop', 'deliveryCategory', 'packaging')->get();
    }

    // Load/batch management removed for on-demand delivery.
}
