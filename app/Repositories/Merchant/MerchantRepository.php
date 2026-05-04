<?php

namespace App\Repositories\Merchant;

use App\Enums\DocumentStatus;
use App\Http\Services\SmsService;
use App\Models\Backend\DeliveryCharge;
use App\Models\Backend\Merchant;
use App\Models\Backend\MerchantDeliveryCharge;
use App\Models\Backend\Upload;
use App\Enums\Status;
use App\Enums\UserType;
use App\Enums\VerificationStatus;
use App\Mail\MerchantSignup;
use App\Mail\MerchantSignupVerified;
use App\Models\Backend\City;
use App\Models\Backend\Country;
use App\Models\Backend\District;
use App\Models\Backend\MerchantPaymentDate;
use App\Models\Backend\Town;
use App\Models\MerchantShops;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Repositories\Merchant\MerchantInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class MerchantRepository implements MerchantInterface
{
    public function all()
    {
        return Merchant::with('user', 'user.upload')
            ->where(function ($query) {
                $query->whereHas('user', function ($queryUser) {
                    if (request()->province_id) {
                        $queryUser->where('province_id', request()->province_id);
                    }
                    if (request()->city_id) {
                        $queryUser->where('city_id', request()->city_id);
                    }
                    if (request()->name) {
                        $queryUser->where('name', 'Like', '%' . request()->name . '%');
                    }
                    if (request()->email) {
                        $queryUser->where('email', request()->email);
                    }
                    if (request()->phone) {
                        $queryUser->where('mobile', request()->phone);
                    }
                });
                // account_type removed for MVP
                if (request()->merchant_unique_id) {
                    $query->where('merchant_unique_id', request()->merchant_unique_id);
                }
            })->orderByDesc('id')
            ->paginate(10);
    }

    public function gatAll()
    {
        return Merchant::with('user', 'user.upload')->orderByDesc('id')->get();
    }

    public function merchantIdlist()
    {
        return Merchant::orderByDesc('id')->select('id', 'business_name')->get();
    }

    public function all_country()
    {
        return Country::all();
    }

    //delivery cahrge location show
    public function toCountries($request)
    {
        return DeliveryCharge::where(function ($query) {
            $query->where('delivery_type_id', request()->delivery_type_id);
            $query->where('from_country_id', request()->from_country_id);
            $query->where('from_city_id', request()->from_city_id);
            $query->where('from_district_id', request()->from_district_id);
            $query->where('from_town_id', request()->from_town_id);
            $query->where('from_portal_code', request()->from_portal_code);
        })->get();
    }

    public function toCities($request)
    {
        return DeliveryCharge::where(function ($query) {
            $query->where('delivery_type_id', request()->delivery_type_id);
            $query->where('from_country_id', request()->from_country_id);
            $query->where('from_city_id', request()->from_city_id);
            $query->where('from_district_id', request()->from_district_id);
            $query->where('from_town_id', request()->from_town_id);
            $query->where('from_portal_code', request()->from_portal_code);
            $query->where('to_country_id', request()->to_country_id);
        })->get();
    }

    public function toDistrict($request)
    {
        return DeliveryCharge::where(function ($query) {
            $query->where('delivery_type_id', request()->delivery_type_id);
            $query->where('from_country_id', request()->from_country_id);
            $query->where('from_city_id', request()->from_city_id);
            $query->where('from_district_id', request()->from_district_id);
            $query->where('from_town_id', request()->from_town_id);
            $query->where('from_portal_code', request()->from_portal_code);
            $query->where('to_country_id', request()->to_country_id);
            $query->where('to_city_id', request()->to_city_id);
        })->get();
    }

    public function toTown($request)
    {
        return DeliveryCharge::where(function ($query) {
            $query->where('delivery_type_id', request()->delivery_type_id);
            $query->where('from_country_id', request()->from_country_id);
            $query->where('from_city_id', request()->from_city_id);
            $query->where('from_district_id', request()->from_district_id);
            $query->where('from_town_id', request()->from_town_id);
            $query->where('from_portal_code', request()->from_portal_code);
            $query->where('to_country_id', request()->to_country_id);
            $query->where('to_city_id', request()->to_city_id);
            $query->where('to_district_id', request()->to_district_id);
        })->get();
    }

    public function toPortalCode($request)
    {
        return DeliveryCharge::find($request->id);
    }

    public function all_city()
    {
        return City::all();
    }

    public function all_district()
    {
        return District::all();
    }

    public function all_town()
    {
        return Town::all();
    }

    public function get($id)
    {
        return Merchant::with('user', 'licensefile', 'nidfile')->find($id);
    }


    //merchan shop get
    public function merchant_shops_get($id)
    {
        return MerchantShops::where('merchant_id', $id)->get();
    }

    //Store merchant data
    public function store($request)
    {
        try {
            DB::beginTransaction();
            $cod_charges = [];
            foreach ($request->area as $key => $area) {
                $cod_charges[$area] = $request->charge[$area];
            }

            $merchantUser                       = new User();
            $merchantUser->first_name           = $request->first_name;
            $merchantUser->last_name            = $request->last_name;
            $merchantUser->name                 = $request->first_name . ' ' . $request->last_name;
            $merchantUser->country_code         = $request->country_code;
            $merchantUser->mobile               = $request->mobile;
            $merchantUser->email                = $request->email;
            $merchantUser->password             = Hash::make($request->password);
            $merchantUser->address                = $request->address;
            $merchantUser->province_id          = $request->province_id;
            $merchantUser->city_id              = $request->city_id;
            $merchantUser->district_id          = $request->district_id;
            $merchantUser->town_id              = $request->town_id;
            $merchantUser->location             = $request->location;
            $merchantUser->latitude             = $request->location_lat;
            $merchantUser->longitude            = $request->location_long;
            $merchantUser->status               = $request->status;
            $merchantUser->user_type            = UserType::MERCHANT;
            $merchantUser->mobile_verified_at = now();
            $merchantUser->email_verified_at  = now();
            if (isset($request->image_id) && $request->image_id != null) {
                $merchantUser->image_id = $this->merchant_image($request->image_id, $merchantUser->image_id);
            }
            $merchantUser->save();
            $merchant                       = new Merchant();
            $merchant->user_id              = $merchantUser->id;
            $merchant->account_type         = 1;
            $merchant->business_name        = $request->business_name ?: ($request->first_name . ' ' . $request->last_name);
            $merchant->merchant_unique_id   = $this->generateUniqueID();

            if ($request->opening_balance !== "") {
                $merchant->current_balance      = $request->opening_balance;
                $merchant->opening_balance      = $request->opening_balance;
            }
            if ($request->vat !== "") {
                $merchant->vat                  = $request->vat;
            }
            $merchant->discount                = $request->discount;
            $merchant->minimum_reaches_amount  = $request->minimum_reaches_amount;

            $merchant->cod_charges                  = $cod_charges;
            $merchant->address                      = $request->business_address;
            $merchant->alternative_phone_number     = $request->alternative_phone_number;

            if (isset($request->nid) && $request->nid != null) {
                $merchant->nid_id = $this->merchaant_nid($request->nid, $merchant->nid_id);
            }
            if (isset($request->nid_back) && $request->nid_back != null) {
                $merchant->nid_back_id = $this->merchaant_nidback($request->nid_back, $merchant->nid_back_id);
            }

            if (isset($request->trade_license) && $request->trade_license != null) {
                $merchant->trade_license = $this->trade_license($request->trade_license, $merchant->trade_license);
            }

            $merchant->payout_date = $request->payout_date;
            if (isset($request->contract_document) && $request->contract_document != null) {
                $merchant->contract_document = $this->contract_document($request->contract_document, '');
            }

            if ($request->payment_period) :
                $merchant->payment_period = $request->payment_period == 0 ? 0 : $request->payment_period;
            else :
                $merchant->payment_period = $request->payment_period == 0 ? 0 : $request->payment_period;
            endif;

            $merchant->return_charges         = $request->return_charges ? $request->return_charges : 100;
            $merchant->reference_name         = $request->reference_name;
            $merchant->reference_phone        = $request->reference_phone;
            $merchant->save();
            if ($merchant) {
                if (count($request->payment_date) > 0) {
                    foreach ($request->payment_date as $value) {
                        MerchantPaymentDate::create([
                            'merchant_id'   => $merchant->id,
                            'date'          => $value,
                        ]);
                    }
                }
            }
            $shop               = new MerchantShops();
            $shop->merchant_id  = $merchant->id;
            $shop->name         = $merchant->business_name;
            $shop->contact_no   = $request->mobile;
            $shop->address      = $request->address;
            $shop->address      = $request->business_address ?: $request->address;
            $shop->status       = $request->status;
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

            DB::commit();
            try {
                if ($merchantUser && $merchant) :
                    $data = [
                        'merchant'      => $merchant,
                        'merchant_user' => $merchantUser
                    ];
                    Mail::to($merchantUser->email)->send(new MerchantSignup($data));
                endif;
            } catch (\Throwable $th) {
                // dd($th);
            }
            if ($request->from_parcel) :
                return $merchant;
            endif;
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    //Sign up store merchant data
    public function signUpStore($request)
    {
        try {
            DB::beginTransaction();
            $full_name = $request->first_name . ' ' . $request->last_name;
            $token = Str::random(64);
            $otp                                = random_int(100000, 999999);
            $merchantUser                       = new User();
            $merchantUser->first_name           = $request->first_name;
            $merchantUser->last_name            = $request->last_name;
            $merchantUser->name                 = $full_name;
            $merchantUser->country_code         = $request->country_code;
            $merchantUser->mobile               = $request->mobile;
            $merchantUser->email                = $request->email;
            $merchantUser->address                = $request->address;
            $merchantUser->password             = Hash::make($request->password);
            $merchantUser->user_type            = UserType::MERCHANT;
            $merchantUser->status               = Status::INACTIVE;
            $merchantUser->verification_status  = VerificationStatus::NOT_VERIFIED;
            $merchantUser->document_status      = DocumentStatus::NOT_VERIFIED;
            $merchantUser->submit_status        = Status::INACTIVE;
            $merchantUser->email_verify_token   = $token;
            $merchantUser->otp                  = $otp;
            $merchantUser->province_id          = $request->province_id;
            $merchantUser->city_id              = $request->city_id;
            $merchantUser->location             = $request->location;
            $merchantUser->permissions          = [];
            $merchantUser->save();

            $merchant                           = new Merchant();
            $merchant->user_id                  = $merchantUser->id;
            $merchant->account_type             = 1;
            $merchant->business_name            = $request->business_name ?: $full_name;
            $merchant->address                  = $request->business_address ?: $request->address;
            $merchant->rc_number      = $request->rc_number;
            $merchant->nif_number      = $request->nif_number;
            if (isset($request->sender_document) && $request->sender_document != null) {
                $merchant->sender_document = $this->sender_document($request->sender_document);
            }
            if (isset($request->sender_document1) && $request->sender_document1 != null) {
                $merchant->sender_document1 = $this->sender_document1($request->sender_document1);
            }
            $merchant->merchant_unique_id       = $this->generateUniqueID();
            $merchant->cod_charges              = array(
                'inside_city'    => "0",
                'sub_city'       => "0",
                'outside_city'   => "0",
            );
            $merchant->opening_balance          = 0;
            $merchant->vat                      = 0;
            $merchant->save();

            $shop               = new MerchantShops();
            $shop->merchant_id  = $merchant->id;
            $shop->name         = $merchant->business_name;
            $shop->contact_no   = $request->mobile;
            if ($request->status) :
                $shop->status       = $request->status;
            else :
                $shop->status       = Status::ACTIVE;
            endif;
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

            session([
                'otp'       => $otp,
                'mobile'    => $request->mobile,
                'email'     => $request->email,
                'password'  => $request->password
            ]);
            // $response =  app(SmsService::class)->sendOtp($request->mobile, $merchantUser->otp);
            try {
                if ($merchantUser && $merchant) :
                    $data = [
                        'merchant_user' => $merchantUser,
                        'otp'           => $merchantUser->otp
                    ];
                    Mail::to($merchantUser->email)->send(new MerchantSignupVerified($data));
                endif;
            } catch (\Throwable $th) {
                // dd($th);
            }
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    //merchant document submit
    public function merchantDocumentSubmit($request)
    {
        try {
            DB::beginTransaction();
            $merchant                           = Merchant::find($request->merchant_id);
            if (isset($request->nid) && $request->nid != null) {
                $merchant->nid_id = $this->merchaant_nid($request->nid, $merchant->nid_id);
            }
            if (isset($request->nid_back) && $request->nid_back != null) {
                $merchant->nid_back_id = $this->merchaant_nidback($request->nid_back, $merchant->nid_back_id);
            }
            if (isset($request->trade_license) && $request->trade_license != null) {
                $merchant->trade_license = $this->trade_license($request->trade_license, $merchant->trade_license);
            }
            $merchant->payout_date = $request->payout_date;
            if (isset($request->contract_document) && $request->contract_document != null) {
                $merchant->contract_document = $this->contract_document($request->contract_document, '');
            }
            $merchant->save();

            $merchantUser = User::find($merchant->user_id);
            if (isset($request->image_id) && $request->image_id != null) {
                $merchantUser->image_id = $this->merchant_image($request->image_id, $merchantUser->image_id);
            }
            $merchantUser->submit_status        = Status::ACTIVE;
            $merchantUser->save();
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    public function accountStatus($id)
    {
        try {
            $merchant                           = Merchant::find($id);
            $merchantUser = User::find($merchant->user_id);
            if ($merchantUser->status == Status::ACTIVE) {
                $merchantUser->status        = Status::INACTIVE;
            } else {
                $merchantUser->status        = Status::ACTIVE;
            }
            $merchantUser->save();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function verificationStatus($id)
    {
        try {
            $merchant                           = Merchant::find($id);
            $merchantUser = User::find($merchant->user_id);
            if ($merchantUser->verification_status == Status::ACTIVE) {
                $merchantUser->verification_status = Status::INACTIVE;
            } else {
                $merchantUser->verification_status = Status::ACTIVE;
            }
            $merchantUser->save();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function documentStatus($id)
    {
        try {
            $merchant                           = Merchant::find($id);
            $merchantUser = User::find($merchant->user_id);
            if ($merchantUser->document_status == Status::ACTIVE) {
                $merchantUser->document_status = Status::INACTIVE;
            } else {
                $merchantUser->document_status = Status::ACTIVE;
            }
            $merchantUser->save();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    // Resend OTP
    public function resendOTP($request)
    {
        try {
            $otp                                = random_int(10000, 99999);
            $merchantUser = User::where('mobile', $request->mobile)->first();
            $merchantUser->otp                  = $otp;
            $merchantUser->save();
            session([
                'otp'     => $otp,
                'mobile'  => $request->mobile,
            ]);
            app(SmsService::class)->sendOtp($merchantUser->mobile, $merchantUser->otp);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    // OTP verification
    public function otpVerification($request)
    {
        try {
            $merchantUser     = User::where('mobile', $request->mobile)->where('otp', $request->otp)->first();
            if ($merchantUser != null) {
                $merchantUser->verification_status = Status::ACTIVE;
                $merchantUser->email_verified_at = now();
                $merchantUser->mobile_verified_at = now();
                $merchantUser->otp = null;
                $merchantUser->save();
                return $merchantUser;
            } else
                return 0;
        } catch (\Exception $e) {
            return false;
        }
    }

    //update merchant data
    public function update($id, $request)
    {
        try {
            DB::beginTransaction();
            $merchant = Merchant::find($id);
            $cod_charges = [];
            foreach ($request->area as $area) {
                $cod_charges[$area] = $request->charge[$area];
            }

            $merchantUser                       = User::find($merchant->user_id);
            $merchantUser->first_name           = $request->first_name;
            $merchantUser->last_name            = $request->last_name;
            $merchantUser->name                 =  $request->first_name . ' ' . $request->last_name;
            $merchantUser->country_code         = $request->country_code;
            $merchantUser->mobile               = $request->mobile;
            $merchantUser->email                = $request->email;
            if ($request->password != null) {
                $merchantUser->password           = Hash::make($request->password);
            }
            $merchantUser->address  = $request->business_address ?: $request->address;
            $merchantUser->user_type              = UserType::MERCHANT;
            $merchantUser->province_id            = $request->province_id;
            $merchantUser->city_id                = $request->city_id;
            $merchantUser->location               = $request->location;
            $merchantUser->latitude               = $request->location_lat;
            $merchantUser->longitude              = $request->location_long;
            $merchantUser->status               = $request->status;
            if ($request->image_id != null) {
                $merchantUser->image_id = $this->merchant_image($request->image_id, $merchantUser->image_id);
            }
            $merchantUser->save();
            // Merchant row
            $merchant->account_type        = 1;
            $merchant->business_name        = $request->business_name ?: ($request->first_name . ' ' . $request->last_name);
            if ($request->opening_balance !== "") {
                $merchant->current_balance      = $request->opening_balance;
                $merchant->opening_balance      = $request->opening_balance;
            };
            if ($request->vat !== "") {
                $merchant->vat                  = $request->vat;
            }
            $merchant->discount                 = $request->discount;
            $merchant->minimum_reaches_amount   = $request->minimum_reaches_amount;
            $merchant->cod_charges                  = $cod_charges;
            $merchant->address                      = $request->business_address ?: $request->address;
            $merchant->alternative_phone_number     = $request->alternative_phone_number;

            if (isset($request->nid) && $request->nid != null) {
                $merchant->nid_id = $this->merchaant_nid($request->nid, $merchant->nid_id);
            }

            if (isset($request->nid_back) && $request->nid_back != null) {
                $merchant->nid_back_id = $this->merchaant_nidback($request->nid_back, $merchant->nid_back_id);
            }

            if (isset($request->trade_license) && $request->trade_license != null) {
                $merchant->trade_license = $this->trade_license($request->trade_license, $merchant->trade_license);
            }

            $merchant->payout_date = $request->payout_date;
            if (isset($request->contract_document) && $request->contract_document != null) {
                $merchant->contract_document = $this->contract_document($request->contract_document, $merchant->contract_document);
            }

            if ($request->payment_period) :
                $merchant->payment_period = $request->payment_period == 0 ? 0 : $request->payment_period;
            else :
                $merchant->payment_period = $request->payment_period == 0 ? 0 : $request->payment_period;
            endif;

            if ($request->return_charges) :
                $merchant->return_charges            = $request->return_charges;
            endif;
            if ($request->reference_name) :
                $merchant->reference_name        = $request->reference_name;
            endif;
            if ($request->reference_phone) :
                $merchant->reference_phone       = $request->reference_phone;
            endif;
            $merchant->save();
            if ($merchant) {
                if (count($request->payment_date) > 0) {
                    MerchantPaymentDate::where('merchant_id', $merchant->id)->delete();
                    foreach ($request->payment_date as $value) {
                        MerchantPaymentDate::create([
                            'merchant_id'   => $merchant->id,
                            'date'          => $value,
                        ]);
                    }
                }
            }
            DB::commit();
            return true;
        } catch (\Exception $e) {
            report($e);
            DB::rollBack();
            return false;
        }
    }

    // for merchant image upload
    public function merchant_image($image, $image_id = '')
    {
        try {
            $image_name = '';
            if (!blank($image)) {
                $destinationPath       = public_path('uploads/merchant/image');
                $merchantImage         = date('YmdHis') . "." . $image->getClientOriginalExtension();
                $image->move($destinationPath, $merchantImage);
                $image_name            = 'uploads/merchant/image/' . $merchantImage;
            }

            if (blank($image_id)) {
                $upload           = new Upload();
            } else {
                $upload           = Upload::find($image_id);
                if (file_exists($upload->original)) {
                    unlink($upload->original);
                }
            }

            $upload->original     = $image_name;
            $upload->save();
            return $upload->id;
        } catch (\Exception $e) {
            return false;
        }
    }
    // for trade_license upload
    public function trade_license($image, $trade_license  = '')
    {
        try {

            $image_name = '';
            if (!blank($image)) {
                $destinationPath       = public_path('uploads/merchant/trade_license');
                $tradeLicense          = date('YmdHis') . "." . $image->getClientOriginalExtension();
                $image->move($destinationPath, $tradeLicense);
                $image_name            = 'uploads/merchant/trade_license/' . $tradeLicense;
            }

            if (blank($trade_license)) {
                $upload           = new Upload();
            } else {
                $upload           = Upload::find($trade_license);
                if (file_exists($upload->original)) {
                    unlink($upload->original);
                }
            }

            $upload->original     = $image_name;
            $upload->save();
            return $upload->id;
        } catch (\Exception $e) {
            return false;
        }
    }

    // for contract_document upload
    public function contract_document($image, $old_data  = '')
    {
        try {
            $image_name = '';
            if (!blank($image)) {
                $destinationPath       = public_path('uploads/merchant/contract_document');
                $tradeLicense          = date('YmdHis') . "." . $image->getClientOriginalExtension();
                $image->move($destinationPath, $tradeLicense);
                $image_name            = 'uploads/merchant/contract_document/' . $tradeLicense;
            }

            if (blank($old_data)) {
                $upload           = new Upload();
            } else {
                $upload           = Upload::find($old_data);
                if (file_exists($upload->original)) {
                    unlink($upload->original);
                }
            }
            $upload->original     = $image_name;
            $upload->save();
            return $upload->id;
        } catch (\Exception $e) {
            return false;
        }
    }

    // for merchant nid upload
    public function merchaant_nid($image, $nid_id  = '')
    {
        try {
            $image_name = '';
            if (!blank($image)) {
                $destinationPath = public_path('uploads/merchant/nid');
                $nid             = date('YmdHis') . random_int(1000, 9000) . "." . $image->getClientOriginalExtension();
                $image->move($destinationPath, $nid);
                $image_name      = 'uploads/merchant/nid/' . $nid;
            }
            if (blank($nid_id)) {
                $upload           = new Upload();
            } else {
                $upload           = Upload::find($nid_id);
                if (file_exists($upload->original)) {
                    unlink($upload->original);
                }
            }

            $upload->original     = $image_name;
            $upload->save();
            return $upload->id;
        } catch (\Exception $e) {
            return false;
        }
    }

    // for merchant nid upload
    public function merchaant_nidback($image, $nid_id  = '')
    {
        try {
            $image_name = '';
            if (!blank($image)) {
                $destinationPath = public_path('uploads/merchant/nid');
                $nid             = date('YmdHis') . random_int(1000, 9000) . "." . $image->getClientOriginalExtension();
                $image->move($destinationPath, $nid);
                $image_name      = 'uploads/merchant/nid/' . $nid;
            }
            if (blank($nid_id)) {
                $upload           = new Upload();
            } else {
                $upload           = Upload::find($nid_id);
                if (file_exists($upload->original)) {
                    unlink($upload->original);
                }
            }

            $upload->original     = $image_name;
            $upload->save();
            return $upload->id;
        } catch (\Exception $e) {
            return false;
        }
    }

    // for sender document upload
    public function sender_document($image)
    {
        try {
            $image_name = '';
            if (!blank($image)) {
                $destinationPath = public_path('uploads/clients');
                $nid             = date('YmdHis') . "." . $image->getClientOriginalExtension();
                $image->move($destinationPath, $nid);
                $image_name      = 'uploads/clients/' . $nid;
            }
            return $image_name;
        } catch (\Exception $e) {
            return false;
        }
    }

    // for sender document upload
    public function sender_document1($image)
    {
        try {
            $image_name = '';
            if (!blank($image)) {
                $destinationPath = public_path('uploads/clients');
                $nid             = '2-' . date('YmdHis') . "." . $image->getClientOriginalExtension();
                $image->move($destinationPath, $nid);
                $image_name      = 'uploads/clients/' . $nid;
            }
            return $image_name;
        } catch (\Exception $e) {
            return false;
        }
    }

    // for unique id ganarate
    public function generateUniqueID()
    {
        do {
            $merchant_unique_id = random_int(100000, 999999);
        } while (Merchant::where("merchant_unique_id", "=", $merchant_unique_id)->first());

        return $merchant_unique_id;
    }

    public function delete($id)
    {
        // try {
        // Find merchant row
        $merchant = Merchant::find($id);
        // Find user row
        $user     = User::find($merchant->user_id);


        $upload     = Upload::find($user->image_id);
        if ($upload != null && file_exists($upload->original)) {
            unlink($upload->original);
            $upload->delete();
        }
        $user->delete();


        $nid     = Upload::find($merchant->nid_id);
        if ($nid != null && file_exists($nid->original)) {
            unlink($nid->original);
            $nid->delete();
        }


        $trade_license     = Upload::find($merchant->trade_license);
        if ($trade_license != null && file_exists($trade_license->original)) {
            unlink($trade_license->original);
            $trade_license->delete();
        }

        // Delete merchant row
        $merchant->delete();

        return true;
        // }
        // catch (\Exception $e) {
        //     return false;
        // }
    }




    //social login merchant signup

    public function socialSignupStore($request, $social)
    {

        try {
            DB::beginTransaction();
            $otp                                = random_int(10000, 99999);

            $merchantUser                       = new User();
            $merchantUser->name                 = $request->name;
            $merchantUser->email                = $request->email;
            if ($social == 'google') :
                $merchantUser->google_id        = $request->id;
            elseif ($social == 'facebook') :
                $merchantUser->facebook_id      = $request->id;
            endif;
            $merchantUser->image_id             = $this->linktoAvatarUpload($request->avatar_original, $request);
            $merchantUser->password             = Hash::make(Str::random(32));
            $merchantUser->user_type            = UserType::MERCHANT;
            $merchantUser->role_id              = null;
            $merchantUser->permissions          = [];

            $merchantUser->save();

            $merchant                           = new Merchant();
            $merchant->user_id                  = $merchantUser->id;
            $merchant->business_name            = $request->name;
            $merchant->merchant_unique_id       = $this->generateUniqueID();
            $merchant->cod_charges              = array(
                'inside_city'    => "0",
                'sub_city'       => "0",
                'outside_city'   => "0",
            );
            $merchant->opening_balance          = 0;
            $merchant->vat                      = 0;
            $merchant->save();

            $shop               = new MerchantShops();
            $shop->merchant_id  = $merchant->id;
            $shop->name         = $merchant->business_name;
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

            DB::commit();
            return $merchantUser;
        } catch (\Exception $e) {

            DB::rollBack();
            return false;
        }
    }


    protected function linktoAvatarUpload($avatar_original, $user = null)
    {
        try {
            //profile upload
            $file             = file_get_contents($avatar_original);
            $file_name        = date('YmdHisA') . $user->id . '.jpg';
            if (!File::isDirectory(public_path('uploads/profile'))) :
                File::makeDirectory(public_path('uploads/profile'));
            endif;
            File::put(public_path('uploads/profile/') . $file_name, $file);
            $file_full_path   = 'uploads/profile/' . $file_name;
            $upload           = new Upload();
            $upload->original = $file_full_path;
            $upload->save();
            //end profile upload

            return $upload->id;
        } catch (\Throwable $th) {
            return null;
        }
    }

    public function countryByCity($id)
    {
        $cities = City::where('country_id', $id)->get();
        return $cities;
    }

    public function cityByDistrict($id)
    {
        $districts = District::where('city_id', $id)->get();
        return $districts;
    }

    public function districtByTown($id)
    {
        $towns = Town::where('district_id', $id)->get();
        return $towns;
    }

    public function townByPortalCode($id)
    {
        $towns = Town::find($id);
        return $towns;
    }

    public function townByCity($id)
    {
        $cities = City::where('id', $id)->get();
        return $cities;
    }

}
