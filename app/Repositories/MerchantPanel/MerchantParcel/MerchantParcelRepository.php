<?php

namespace App\Repositories\MerchantPanel\MerchantParcel;

use App\Enums\ParcelStatus;
use App\Enums\Status;
use App\Enums\UserType;
use App\Http\Services\PushNotificationService;
use App\Models\Backend\Deliverycategory;
use App\Models\Backend\DeliveryCharge;

use App\Models\Backend\ParcelLogs;
use App\Models\Backend\Merchant;
use App\Models\Backend\MerchantDeliveryCharge;
use App\Models\Backend\Packaging;
use App\Models\Backend\Parcel;
use App\Models\MerchantShops;
use Carbon\Carbon;
use App\Models\Backend\ParcelEvent;
use App\Models\Backend\ParcelItem;
use App\Models\Backend\Receiver;
use App\Models\Backend\SenderCustomer;
use App\Models\Backend\ShippingType;
use App\Models\Config;
use App\Models\Subscribe;
use App\Models\User;
use App\Enums\InvoiceStatus;
use App\Enums\WhoPays;
use App\Helpers\DeliveryChargeHelper;
use App\Services\MpesaStkPushService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class MerchantParcelRepository implements MerchantParcelInterface
{
    private ?string $lastError = null;

    public function getLastError(): ?string
    {
        return $this->lastError;
    }

    public function all($merchant_id)
    {
        return Parcel::where('merchant_id', $merchant_id)->orderByDesc('id')->paginate(10);
    }

    public function activeLiveMonitoring($merchant_id, $request)
    {
        return Parcel::where('merchant_id', $merchant_id)
            ->whereNotIn('status', [
                ParcelStatus::DELIVERED,
                ParcelStatus::PARTIAL_DELIVERED,
                ParcelStatus::RETURN_RECEIVED_BY_MERCHANT,
                ParcelStatus::PARCEL_CANCEL
            ])
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

                if ($request->payment_status) {
                    $query->where('payment_status', $request->payment_status);
                }
            })->orderByDesc('id')->paginate(10);
    }

    public function passiveMonitoring($merchant_id, $request)
    {
        return Parcel::where('merchant_id', $merchant_id)
            ->whereIn('status', [
                ParcelStatus::DELIVERED,
                ParcelStatus::PARTIAL_DELIVERED,
                ParcelStatus::RETURN_RECEIVED_BY_MERCHANT,
                ParcelStatus::PARCEL_CANCEL
            ])
            ->where(function ($query) use ($request) {

                if ($request->parcel_date) {
                    $date = explode('To', $request->parcel_date);
                    if (is_array($date)) {
                        $from   = Carbon::parse(trim($date[0]))->startOfDay()->toDateTimeString();
                        $to     = Carbon::parse(trim($date[1]))->endOfDay()->toDateTimeString();
                        $query->whereBetween('created_at', [$from, $to]);
                    }
                }

                if ($request->parcel_status) {
                    if ($request->parcel_status == ParcelStatus::DELIVERED) :
                        $query->whereIn('status', [ParcelStatus::DELIVERED, ParcelStatus::PARTIAL_DELIVERED]);
                    else :
                        $query->where('status', $request->parcel_status);
                    endif;
                } else {
                    $query->whereIn('status', [ParcelStatus::DELIVERED, ParcelStatus::PARTIAL_DELIVERED]);
                }

                if ($request->parcel_customer) {
                    $query->where('customer_name', 'like', '%' . $request->parcel_customer . '%');
                }
                if ($request->parcel_customer_phone) {
                    $query->where('customer_phone', 'like', '%' . $request->parcel_customer_phone . '%');
                }
                if ($request->invoice_id) {
                    $query->where('invoice_no', 'like', '%' . $request->invoice_id . '%');
                }
                // shipping_type removed (online-only marketplace flow)
            })
            ->orderByDesc('id')->paginate(10);
    }

    public function receivedParcels()
    {
        return Parcel::where('merchant_id', Auth::user()->merchant->id)
            ->where('status', ParcelStatus::DELIVERED)
            ->orderByDesc('id')
            ->paginate(10);
    }

    public function parcelAll($merchant_id)
    {
        return Parcel::with('merchant')->where('merchant_id', $merchant_id)->orderByDesc('id')->get();
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

    public function parcelBank($merchant_id)
    {
        return Parcel::where('parcel_bank', "on")->where('merchant_id', $merchant_id)->orderByDesc('id')->paginate(10);
    }

    public function filter($merchant_id, $request)
    {
        return Parcel::where('merchant_id', $merchant_id)
            ->with('parcelEvent')
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

    public function parcelEvents($id)
    {
        return ParcelEvent::where('parcel_id', $id)->orderBy('created_at', 'desc')->get();
    }

    public function get($id)
    {
        return Parcel::find($id);
    }

    public function details($id)
    {
        return Parcel::where('id', $id)->with('merchant', 'merchant.user', 'merchantShop', 'deliveryCategory', 'packaging')->first();
    }

    public function statusUpdate($id, $status_id)
    {
        $parcel         = Parcel::find($id);
        $parcel->status = $status_id;
        $parcel->save();
        return true;
    }

    public function getMerchant($id)
    {
        return Merchant::where('user_id', $id)->first();
    }

    public function getShop($id)
    {
        return MerchantShops::where('merchant_id', $id)->first();
    }

    public function getShops($id)
    {
        $merchantShops      = [];
        $merchantShop       = MerchantShops::where(['merchant_id' => $id, 'default_shop' => Status::ACTIVE])->first();
        $merchantShops[]    = $merchantShop;
        $merchantShopArray  = MerchantShops::where(['merchant_id' => $id, 'default_shop' => Status::INACTIVE])->get();
        if (!blank($merchantShopArray)) {
            foreach ($merchantShopArray as $shop) {
                $merchantShops[] = $shop;
            }
        }
        return $merchantShops;
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

    public function store($request, $merchant_id)
    {

        try {
            $this->lastError = null;
            DB::beginTransaction();
            $chargeDetails = json_decode($request->chargeDetails);
            $parcel                         = new Parcel();
            $parcel->merchant_id            = $request->merchant_id ?? $merchant_id;

            $parcel->sender_company_name  = $request->company_name;
            $parcel->sender_first_name    = $request->first_name;
            $parcel->sender_last_name     = $request->last_name;
            $parcel->sender_email         = $request->sender_email;
            $parcel->sender_phone         = $request->pickup_phone;
            $parcel->sender_residential_address = $request->residential_address;


            $parcel->category_id            = $request->category_id;
            if ($request->weight) {
                $parcel->weight                 = $request->weight;
            }

            if ($request->selling_price) {
                $parcel->selling_price          = $request->selling_price;
            }

            if ($request->parcel_value) {
                $parcel->parcel_value           = $request->parcel_value;
            }

            if ($request->hasFile('parcel_file')) {
                $file = $request->file('parcel_file');
                $destinationPath = public_path('uploads/parcel');
                if (!File::exists($destinationPath)) {
                    File::makeDirectory($destinationPath, 0755, true);
                }
                $fileName = date('YmdHis') . "." . $file->getClientOriginalExtension();
                $file->move($destinationPath, $fileName);
                $parcel->parcel_file = 'uploads/parcel/' . $fileName;
            }

            //location type
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
            if ($request->receiver_whatsapp_number) {
                $parcel->receiver_whatsapp_number   = $request->receiver_whatsapp_number;
            }

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
            $parcel->customer_id = null;

            $parcel->pick_type = 1;
            $parcel->pickup_date = Carbon::now()->format('Y-m-d H:i:s');
            $parcel->delivery_date = Carbon::now()->format('Y-m-d H:i:s');

            $parcel->discount         = $request->merchant_discount;
            $parcel->discount_amount  = $request->merchant_discount_amount;
            $parcel->vat                    = $chargeDetails->vatTex;
            $parcel->vat_amount             = $chargeDetails->VatAmount;
            $parcel->delivery_charge        = $chargeDetails->deliveryChargeAmount;

            $parcel->total_delivery_amount  = $chargeDetails->totalDeliveryChargeAmount;
            $parcel->current_payable        = $chargeDetails->currentPayable;
            $parcel->note                   = $request->note;
            $parcel->parcel_bank            = $request->parcel_bank;

            $whoPays = (int) ($request->who_pays_either ?? 0);
            $whoPaysForFlow = $whoPays === WhoPays::RECIPIENT ? WhoPays::RECIPIENT : WhoPays::SENDER;
            $paymentIntent = (string) ($request->payment_intent ?? 'pay_now');
            $paymentConfirmed = (bool) ($request->payment_confirmed ?? false);

            if ($paymentConfirmed) {
                $parcel->status = ParcelStatus::MARKETPLACE_PENDING;
                $parcel->payment_status = InvoiceStatus::PAID;
            } elseif ($whoPaysForFlow === WhoPays::RECIPIENT) {
                $parcel->status = ParcelStatus::MARKETPLACE_RECEIVER_PAYMENT_AWAITING;
                $parcel->payment_status = InvoiceStatus::UNPAID;
            } elseif ($whoPaysForFlow === WhoPays::SENDER && $paymentIntent === 'pay_later') {
                $parcel->status = ParcelStatus::MARKETPLACE_SENDER_PAYMENT_AWAITING;
                $parcel->payment_status = InvoiceStatus::UNPAID;
            } else {
                $parcel->status = ParcelStatus::MARKETPLACE_PENDING;
                $parcel->payment_status = InvoiceStatus::PROCESSING;
            }
            $parcel->scheduled_amount           = $chargeDetails->scheduledServiceAmount;
            $parcel->total_extra_cost           = $chargeDetails->totalExtraCost;
            if ($request->packaging_id) {
                $parcel->packaging_id               = $request->packaging_id;
                $parcel->packaging_amount           = $chargeDetails->packagingAmount;
            }
            if (isset($chargeDetails->liquidFragileAmount)) {
                $parcel->liquid_fragile_amount  = $chargeDetails->liquidFragileAmount;
            }
            $trakingID                           = substr(strtotime(date('H:i:s')), 1) . 'C' . $parcel->merchant_id . $parcel->id;
            if (strlen($trakingID) <= 14) {
                $parcel->tracking_id             = Str::upper(settings()->par_track_prefix) . $trakingID;
            } else {
                $parcel->tracking_id             = Str::upper(settings()->par_track_prefix) . substr(strtotime(date('H:i:s')), strlen($trakingID) - 14) . 'C' . $parcel->merchant_id . $parcel->id;
            }


            $parcel->delivery_charge_id = $request->to_town_id;
            $parcel->who_pays_either            = $request->who_pays_either;
            $parcel->receiver_mpesa_phone       = $request->receiver_mpesa_phone ?? null;
            $parcel->special_discount = $request->special_discount;
            $parcel->policy                             = $request->policy == 'on' ? Status::ACTIVE : Status::INACTIVE;

            $distanceKm = (float) ($request->distance_km ?? 0);
            $weightKg = (float) ($request->total_weight ?? $request->local_weight ?? 0);
            $breakdown = DeliveryChargeHelper::instance()->marketplacePricingBreakdown($distanceKm, $weightKg, $whoPaysForFlow);
            $parcel->base_delivery_charge = $breakdown['base'];
            $parcel->receiver_markup = $breakdown['markup'];
            $parcel->final_paid_amount = $breakdown['final'];
            $parcel->commission_percent = (float) (settings()->marketplace_commission_percent ?? 0);

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
                'extra_cost'                    => $request->extra_cost == 'on' ? '1' : null,
                'extra_cost_amount'             => $request->extra_cost_amount ?? 0,
                'extra_cost_description'        => $request->extra_cost_description ?? null,
                'packaging_id'                  => $request->packaging_id ?? null,
                'content_parcel'                => $request->content_parcel ?? null,
            ];

            $parcel->cbm_details        = $cbm_details;
            $parcel->total_weight       = $request->total_weight;
            $parcel->total_cubic_meters = $request->total_cbm;
            $parcel->total_valumetric_weight = $request->total_valumetric_weight; //valumetric weight
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

            $mpesaCheckoutId = trim((string) ($request->mpesa_checkout_request_id ?? ''));
            if ($mpesaCheckoutId !== '') {
                \App\Models\Backend\MpesaPayment::where('checkout_request_id', $mpesaCheckoutId)
                    ->whereNull('parcel_id')
                    ->update(['parcel_id' => $parcel->id]);
            }

            if ($whoPaysForFlow === WhoPays::RECIPIENT && !empty($parcel->receiver_mpesa_phone)) {
                $paymentPayload = [
                    'merchant_id' => optional($parcel->merchant)->user_id,
                    'parcel_id' => $parcel->id,
                    'phone' => $parcel->receiver_mpesa_phone,
                    'amount' => $parcel->final_paid_amount,
                    'account_reference' => 'Receiver Payment',
                    'transaction_desc' => 'Receiver payment for parcel ' . $parcel->tracking_id,
                    'parcel_payload' => [
                        'parcel_id' => $parcel->id,
                        'who_pays' => 'receiver',
                    ],
                ];

                $result = app(MpesaStkPushService::class)->initiate($paymentPayload);
                if (empty($result['success'])) {
                    Log::warning('Receiver M-Pesa prompt failed', [
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
            DB::commit();
            try {
                $super_admins = User::where('user_type', UserType::ADMIN)->get();
                foreach ($super_admins as $super_admin) {
                    $msgNote = 'Merchant: ' . $parcel->merchant->business_name . ', Your parcel is successfully created. Your parcel with ID ' . $parcel->tracking_id;
                    if (isset($super_admin->web_token)) {
                        app(PushNotificationService::class)->sendCreatePushNotificationToMessage($super_admin->web_token, $msgNote);
                    }
                }
            } catch (\Exception $exception) {
                // dd($exception);
            }
            return true;
        } catch (\Exception $e) {
            $this->lastError = $e->getMessage();
            Log::error('Merchant parcel store error', [
                'request' => $request->all(),
                'error' => $e,
            ]);
            DB::rollBack();
            return false;
        }
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
                'package_type_id'                   => $item->package_type_id ?? null,
                'length'                            => $item->length ?? null,
                'width'                             => $item->width ?? null,
                'height'                            => $item->height ?? null,
                'weight'                            => $item->local_weight ?? null,
                'quantity'                          => $item->quantity ?? null,
                'category_id'                       => $item->category_id ?? null,
                'fragile_liquid_amount'             => $item->fragile_liquid ?? null,
                'rush_hour_service'                 => $item->rush_hour_service ?? null,
                'extra_cost'                        => $item->extra_cost ?? null,
                'extra_cost_amount'                 => $item->extra_cost_amount ?? 0,
                'extra_cost_description'            => $item->extra_cost_description ?? null,
                'packaging_id'                      => $item->packaging_id ?? null,
                'description'                       => $item->description ?? null,
                'unit_parcel_service_cost'          => $item->unit_parcel_service_cost ?? 0,
                'total_weight'                      => $item->total_weight ?? null,
                'total_cbm'                         => $item->total_cbm ?? null,
                'content_parcel'                    => $item->content_parcel ?? null,
                'parcel_value'                      => $item->parcel_value ?? 0,
            ]);
            return true;
        } catch (\Throwable $th) {

            return false;
        }
    }

    public function duplicateStore($request, $merchant_id)
    {
        try {
            DB::beginTransaction();
            $merchant                       = Merchant::with('user')->find($request->merchant_id);
            $chargeDetails = json_decode($request->chargeDetails);

            $duplicate_parcel = $this->get($request->parcel_id);

            $parcel                         = new Parcel();
            $parcel->merchant_id            = $request->merchant_id ?? $merchant_id;

            $parcel->sender_company_name  = $request->company_name;
            $parcel->sender_first_name    = $request->first_name;
            $parcel->sender_last_name     = $request->last_name;
            $parcel->sender_email         = $request->sender_email;
            $parcel->sender_phone         = $request->pickup_phone;
            $parcel->sender_residential_address = $request->residential_address;


            if ($request->weight !== "") {
                $parcel->weight                 = $request->weight;
            }

            if ($request->selling_price) {
                $parcel->selling_price          = $request->selling_price;
            }

            if ($request->parcel_value) {
                $parcel->parcel_value           = $request->parcel_value;
            }

            if ($request->hasFile('parcel_file')) {
                $file = $request->file('parcel_file');
                $destinationPath = public_path('uploads/parcel');
                if (!File::exists($destinationPath)) {
                    File::makeDirectory($destinationPath, 0755, true);
                }
                $fileName = date('YmdHis') . "." . $file->getClientOriginalExtension();
                $file->move($destinationPath, $fileName);
                $parcel->parcel_file = 'uploads/parcel/' . $fileName;
            }

            $parcel->from_state_id        = $request->from_state_id;
            $parcel->from_account_type    = $request->from_account_type;
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
            $parcel->to_account_type      = $request->to_account_type;
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
            $parcel->receiver_whatsapp_number   = $request->receiver_whatsapp_number;

            $parcel->merchant_shop_id       = $request->shop_id;
            $parcel->pickup_phone           = $request->pickup_phone;
            $parcel->pickup_address         = $request->pickup_address;

            $parcel->to_merchant_id         = $request->to_merchant_id;
            $parcel->customer_company_name  = $request->customer_company_name;
            $parcel->customer_first_name    = $request->customer_first_name;
            $parcel->customer_last_name     = $request->customer_last_name;
            $parcel->customer_name          = $request->customer_name;
            $parcel->receiver_email         = $request->receiver_email;
            $parcel->customer_phone         = $request->customer_phone;
            $parcel->customer_address       = $request->customer_address;

            if ($request->from_account_type == 1) {
                if (empty($request->to_merchant_id)) {
                    $merchant_check = Merchant::where(function ($query) use ($request) {
                        $query->whereHas('user', function ($userEvent) use ($request) {
                            $userEvent->where([
                                'first_name' => $request->customer_first_name,
                                'last_name' => $request->customer_last_name,
                                'mobile' => $request->customer_phone
                            ]);
                        });
                    })->first();

                    if (empty($merchant_check)) {
                        $merchantUser                       = new User();
                        $merchantUser->first_name           = $request->customer_first_name;
                        $merchantUser->last_name            = $request->customer_last_name;
                        $merchantUser->name                 = $request->customer_first_name . ' ' . $request->customer_last_name;
                        $merchantUser->mobile               = $request->customer_phone;
                        $merchantUser->email                = $request->receiver_email;
                        $merchantUser->password             = Hash::make('123456789');
                        $merchantUser->address              = $request->address;
                        $merchantUser->province_id          = $request->to_state_id;
                        $merchantUser->city_id              = $request->to_city_id;
                        $merchantUser->location             = $request->drop_location;
                        $merchantUser->latitude             = $request->drop_latitude;
                        $merchantUser->longitude            = $request->drop_longitude;
                        $merchantUser->address              = $request->customer_address;
                        $merchantUser->status               = Status::INACTIVE;
                        $merchantUser->user_type            = UserType::MERCHANT;
                        $merchantUser->save();
                        if ($merchantUser) {
                            $merchant                       = new Merchant();
                            $merchant->user_id              = $merchantUser->id;
                            $merchant->account_type         = $request->to_account_type;
                            if ($request->to_account_type == 2) :
                                $merchant->business_name        =  $request->business_name;
                            else :
                                $merchant->business_name        =  $request->first_name . ' ' . $request->last_name;
                            endif;
                            $merchant->merchant_unique_id   = $this->generateUniqueID();
                            $merchant->address                      = $request->customer_address;
                            $merchant->save();

                            $shop               = new MerchantShops();
                            $shop->merchant_id  = $merchant->id;
                            $shop->name         = $merchant->business_name;
                            $shop->contact_no   = $request->mobile;
                            $shop->address      = $request->customer_address;
                            $shop->status       = Status::ACTIVE;
                            $shop->default_shop = Status::ACTIVE;
                            $shop->save();

                            $deliveryCharges =  DeliveryCharge::with('category')->orderBy('position')->get();
                            if (!blank($deliveryCharges)) {
                                foreach ($deliveryCharges as $delivery) {
                                    $deliveryCharge                      = new MerchantDeliveryCharge();
                                    $deliveryCharge->merchant_id         = $merchant->id;
                                    $deliveryCharge->delivery_charge_id  = $delivery->id;
                                    $deliveryCharge->weight              = $delivery->weight;
                                    $deliveryCharge->category_id         = $delivery->category_id;
                                    $deliveryCharge->same_day            = $delivery->same_day;
                                    $deliveryCharge->next_day            = $delivery->next_day;
                                    $deliveryCharge->sub_city            = $delivery->sub_city;
                                    $deliveryCharge->outside_city        = $delivery->outside_city;
                                    $deliveryCharge->status              = Status::ACTIVE;
                                    $deliveryCharge->save();
                                }
                            }
                        }
                        $parcel->to_merchant_id         = $merchant->id;
                    } else {
                        $parcel->to_merchant_id         = $merchant_check->id;
                    }
                } else {
                    $parcel->to_merchant_id         = $request->to_merchant_id;
                }
            } elseif ($request->from_account_type == 2) {
                if (empty($request->customer_id)) {
                    $sender_customer_check = SenderCustomer::where([
                        'first_name' => $request->customer_first_name,
                        'last_name' => $request->customer_last_name,
                        'phone_number' => $request->customer_phone
                    ])->first();
                    if (empty($sender_customer_check)) {
                        $sender_customer                    = new SenderCustomer();
                        $sender_customer->sender_id         = $merchant_id;
                        $sender_customer->province_id       = $request->to_state_id;
                        $sender_customer->city_id           = $request->to_city_id;
                        $sender_customer->account_type      = $request->to_account_type;
                        $sender_customer->first_name        = $request->customer_first_name;
                        $sender_customer->last_name         = $request->customer_last_name;
                        $sender_customer->phone_number      = $request->customer_phone;
                        $sender_customer->email             = $request->receiver_email;
                        $sender_customer->whatsapp_number   = $request->receiver_whatsapp_number;
                        $sender_customer->address           = $request->customer_address;
                        $sender_customer->status            = Status::ACTIVE;
                        $sender_customer->save();
                        $parcel->customer_id         = $sender_customer->id;
                    } else {
                        $parcel->customer_id         = $sender_customer_check->id;
                    }
                } else {
                    $parcel->customer_id         = $request->customer_id;
                }
            }

            $parcel->note                   = $request->note;
            $parcel->parcel_bank            = $request->parcel_bank;
            $parcel->status                 = ParcelStatus::MARKETPLACE_PENDING;

            $parcel->pick_type   = $request->pick_type;
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

            if (!blank($chargeDetails)) {
                $parcel->vat                    = $chargeDetails->vatTex;
                $parcel->vat_amount             = $chargeDetails->VatAmount;
                $parcel->delivery_charge        = $chargeDetails->deliveryChargeAmount;
                $parcel->total_delivery_amount  = $chargeDetails->totalDeliveryChargeAmount;
                $parcel->current_payable        = $chargeDetails->currentPayable;
                $parcel->rush_hour_amount           = $chargeDetails->rushHourServiceAmount;
                $parcel->scheduled_amount           = $chargeDetails->scheduledServiceAmount;
                $parcel->total_extra_cost           = $chargeDetails->totalExtraCost;
                if ($request->packaging_id) {
                    $parcel->packaging_id           = $request->packaging_id;
                    $parcel->packaging_amount       = $chargeDetails->packagingAmount;
                }
                if (isset($chargeDetails->liquidFragileAmount)) {
                    $parcel->liquid_fragile_amount      = $chargeDetails->liquidFragileAmount;
                } else {
                    $parcel->liquid_fragile_amount      = null;
                }
            } else {
                $parcel->vat                    = $duplicate_parcel->vat;
                $parcel->vat_amount             = $duplicate_parcel->vat_amount;
                $parcel->delivery_charge        = $duplicate_parcel->delivery_charge;
                $parcel->total_delivery_amount  = $duplicate_parcel->total_delivery_amount;
                $parcel->current_payable        = $duplicate_parcel->current_payable;
                if ($request->packaging_id) {
                    $parcel->packaging_id           = $request->packaging_id;
                    $parcel->packaging_amount       = $duplicate_parcel->packaging_amount;
                }
                $parcel->liquid_fragile_amount  = $duplicate_parcel->liquid_fragile_amount;
            }

            $trakingID                           =  substr(strtotime(date('H:i:s')), 1) . 'C' . $parcel->merchant_id . $parcel->id;
            if (strlen($trakingID) <= 14) {
                $parcel->tracking_id             = Str::upper(settings()->par_track_prefix) . $trakingID;
            } else {
                $parcel->tracking_id             =  Str::upper(settings()->par_track_prefix) . substr(strtotime(date('H:i:s')), strlen($trakingID) - 14) . 'C' . $parcel->merchant_id . $parcel->id;
            }

            $parcel->delivery_charge_id = $request->to_town_id;
            $parcel->who_pays_either            = $request->who_pays_either;
            $parcel->slots = @$shippingType->slots;
            $parcel->special_discount = $request->special_discount;
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
            $parcel->total_valumetric_weight = $request->total_valumetric_weight; //valumetric weight
            //total cbm


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
            $merchant = Merchant::find($request->merchant_id);
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
            if (!blank($chargeDetails)) {
                $log->total_delivery_amount  = $chargeDetails->totalDeliveryChargeAmount;
                $log->current_payable        = $chargeDetails->currentPayable;
            } else {
                $log->total_delivery_amount  = $duplicate_parcel->total_delivery_amount;
                $log->current_payable        = $duplicate_parcel->current_payable;
            }
            $log->note                   = $request->note;
            $log->save();

            DB::commit();

            try {
                $super_admins = User::where('user_type', UserType::ADMIN)->get();
                foreach ($super_admins as $super_admin) {
                    $msgNote = 'Merchant: ' . $parcel->merchant->business_name . ', Your parcel is successfully duplicate parcel created. Your parcel with ID ' . $parcel->tracking_id;
                    if (isset($super_admin->web_token)) {
                        app(PushNotificationService::class)->sendCreatePushNotificationToMessage($super_admin->web_token, $msgNote);
                    }
                }
            } catch (\Exception $exception) {
                // dd($exception);
            }

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    public function update($id, $request, $merchant_id)
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


            if ($request->weight !== "") {
                $parcel->weight                 = $request->weight;
            }
            // $parcel->invoice_no             = $request->invoice_no;
            if ($request->selling_price) {
                $parcel->selling_price          = $request->selling_price;
            }

            $parcel->from_state_id        = $request->from_state_id;
            $parcel->from_account_type    = $request->from_account_type;
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
            $parcel->to_account_type      = $request->to_account_type;
            $parcel->to_point_type        = $request->to_point_type;
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
            $parcel->customer_first_name    = $request->customer_first_name;
            $parcel->customer_last_name     = $request->customer_last_name;
            $parcel->customer_name          = $request->customer_name;
            $parcel->receiver_email         = $request->receiver_email;
            $parcel->customer_phone         = $request->customer_phone;
            $parcel->customer_address       = $request->customer_address;

            $parcel->pick_type   = $request->pick_type;
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

            $parcel->note                   = $request->note;
            $parcel->parcel_bank            = $request->parcel_bank;

            if (!blank($chargeDetails)) {

                $parcel->vat                    = $chargeDetails->vatTex;
                $parcel->vat_amount             = $chargeDetails->VatAmount;
                $parcel->delivery_charge        = $chargeDetails->deliveryChargeAmount;
                $parcel->total_delivery_amount  = $chargeDetails->totalDeliveryChargeAmount;
                $parcel->current_payable        = $chargeDetails->currentPayable;
                $parcel->rush_hour_amount           = $chargeDetails->rushHourServiceAmount;
                $parcel->scheduled_amount           = $chargeDetails->scheduledServiceAmount;
                $parcel->total_extra_cost           = $chargeDetails->totalExtraCost;
                if (isset($chargeDetails->liquidFragileAmount)) {
                    $parcel->liquid_fragile_amount      = $chargeDetails->liquidFragileAmount;
                } else {
                    $parcel->liquid_fragile_amount      = null;
                }
                if ($request->packaging_id) {
                    $parcel->packaging_id               = $request->packaging_id;
                    $parcel->packaging_amount           = $chargeDetails->packagingAmount;
                }
            }


            $parcel->delivery_charge_id   = $request->to_town_id;
            $parcel->who_pays_either            = $request->who_pays_either;
            $parcel->special_discount = $request->special_discount;
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
            $parcel->total_valumetric_weight = $request->total_valumetric_weight; //valumetric weight
            // total cbm

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
            DB::commit();

            try {
                $super_admins = User::where('user_type', UserType::ADMIN)->get();
                foreach ($super_admins as $super_admin) {
                    $msgNote = 'Merchant: ' . $parcel->merchant->business_name . ', Your parcel is successfully updated. Your parcel with ID ' . $parcel->tracking_id;
                    if (isset($super_admin->web_token)) {
                        app(PushNotificationService::class)->sendCreatePushNotificationToMessage($super_admin->web_token, $msgNote);
                    }
                }
            } catch (\Exception $exception) {
                // dd($exception);
            }

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    public function delete($id, $merchant_id)
    {
        return Parcel::destroy($id);
    }


    public function parcelTrack($track_id)
    {
        $trackValue = trim((string) $track_id);
        if ($trackValue === '') {
            return false;
        }

        $parcel = Parcel::query()
            ->where('tracking_id', $trackValue)
            ->orWhere('tracking_token', $trackValue)
            ->with(['merchant'])
            ->select('id', 'merchant_id', 'tracking_id', 'tracking_token', 'created_at')
            ->first();
        if (!$parcel) {
            return false;
        }
        $merchant = Merchant::find($parcel->merchant_id ?? 0);
        $createdEvent = [
            'tracking_id'   => $parcel->tracking_id,
            'created_at'    => $parcel->created_at,
            'merchant_name' => @$merchant->user->name,
            'email'         => @$merchant->user->email,
            'mobile'        => @$merchant->user->mobile
        ];
        if ($parcel) :
            $data = [
                'parcel' => $createdEvent,
                'events' => ParcelEvent::with(['deliveryMan', 'pickupman', 'transferDeliveryman', 'hub', 'user'])->where('parcel_id', $parcel->id)->orderBy('created_at', 'desc')->get()
            ];
            return $data;
        else :
            return false;
        endif;
    }


    public function subscribe($request)
    {

        try {
            $exists  = Subscribe::where('email', $request->email)->first();
            if ($exists) :
                return 1;
            else :
                try {
                    Subscribe::create(['email' => $request->email]);
                    return true;
                } catch (\Throwable $th) {
                    return false;
                }
            endif;
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function filterExport($merchant_id, $request)
    {
        return Parcel::where('merchant_id', $merchant_id)->orderByDesc('id')->where(function ($query) use ($request) {
            if ($request->parcel_date) {
                $date = explode('To', $request->parcel_date);
                if (is_array($date)) {
                    $from   = Carbon::parse(trim($date[0]))->startOfDay()->toDateTimeString();
                    $to     = Carbon::parse(trim($date[1]))->endOfDay()->toDateTimeString();
                    $query->whereBetween('created_at', [$from, $to]);
                }
            }

            if ($request->parcel_status) {
                $query->where('status', $request->parcel_status);
            }
            if ($request->parcel_customer) {
                $query->where('customer_name', 'like', '%' . $request->parcel_customer . '%');
            }
            if ($request->parcel_customer_phone) {
                $query->where('customer_phone', 'like', '%' . $request->parcel_customer_phone . '%');
            }
        })->get();
    }

    public function parcelExport($request)
    {
        try {
            if ($request->parcel_date !== "" || $request->parcel_status !== "" || $request->parcel_customer !== "" || $request->parcel_customer_phone !== "") :
                $parcels  = $this->filterExport(Auth::user()->merchant->id, $request);
            else :
                $parcels = Parcel::where(['merchant_id' => Auth::user()->merchant->id])->get();
            endif;
            return $parcels;
        } catch (\Throwable $th) {

            return collect([]);
        }
    }

    public function statusWiseParcelList($status)
    {
        return Parcel::where('merchant_id', Auth::user()->merchant->id)->where('status', $status)->paginate(10);
    }
}
