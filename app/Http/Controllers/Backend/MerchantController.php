<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Merchant\StoreRequest;
use App\Http\Requests\Merchant\SignUpRequest;
use App\Http\Requests\Merchant\UpdateRequest;
use App\Http\Requests\Merchant\OtpRequest;
use App\Enums\DocumentStatus;
use App\Enums\Status;
use App\Enums\UserType;
use App\Enums\VerificationStatus;
use App\Models\Backend\City;
use App\Models\Backend\DeliveryCharge;
use App\Models\Backend\Merchant;
use App\Models\Backend\MerchantDeliveryCharge;
use App\Models\Backend\MerchantPaymentDate;
use App\Models\MerchantShops;
use App\Models\User;
use App\Repositories\Merchant\MerchantInterface;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\MerchantSignupVerified;
use App\Http\Services\SmsService;

class MerchantController extends Controller
{
    protected $repo;
    public function __construct(MerchantInterface $repo)
    {
        $this->repo = $repo;
    }

    public function index()
    {
        $merchants = $this->repo->all();
        return view('backend.merchant.index', compact('merchants'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $data['countries'] = $this->repo->all_country();
        $data['cities'] = $this->repo->all_city();
        $data['districts'] = $this->repo->all_district();
        $data['towns'] = $this->repo->all_town();
        $data['request']  = $request;
        return view('backend.merchant.create', $data);
    }

    public function signUp(Request $request)
    {
        $countries       = $this->repo->all_country();
        $cities       = $this->repo->all_city();
        $districts       = $this->repo->all_district();
        $towns       = $this->repo->all_town();
        return view('backend.merchant.sign_up', compact('countries', 'cities', 'districts', 'towns', 'request'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        if ($merchant = $this->repo->store($request)) {
            Toastr::success(__('merchant.added_msg'), __('message.success'));
            if ($request->from_parcel) :
                return redirect()->route('parcel.create', ['merchant_id' => $merchant->id]);
            endif;
            return redirect()->route('customer.index');
        } else {
            Toastr::error(__('merchant.error_msg'), __('message.error'));
            return redirect()->back()->withInput($request->all());
        }
    }

    public function signUpStore(Request $request)
    {
        $request->validate([
            'contact' => ['required', 'string'],
        ]);

        $contact = trim((string) $request->contact);
        $isEmail = filter_var($contact, FILTER_VALIDATE_EMAIL) !== false;
        $mobile = $isEmail ? null : preg_replace('/\D+/', '', $contact);
        $email = $isEmail ? $contact : null;

        if (!$isEmail && (empty($mobile) || strlen($mobile) < 9 || strlen($mobile) > 14)) {
            return redirect()->back()->withErrors(['contact' => 'Enter a valid email or mobile number.'])->withInput();
        }

        $exists = User::query()
            ->when($isEmail, function ($query) use ($email) {
                $query->where('email', $email);
            }, function ($query) use ($mobile) {
                $query->where('mobile', $mobile);
            })
            ->exists();

        if ($exists) {
            return redirect()->back()->withErrors(['contact' => 'This contact is already registered.'])->withInput();
        }

        try {
            DB::beginTransaction();
            $otp = random_int(100000, 999999);

            $merchantUser = new User();
            $merchantUser->first_name = 'Pending';
            $merchantUser->last_name = 'Merchant';
            $merchantUser->name = 'Pending Merchant';
            $merchantUser->country_code = $request->country_code ?? null;
            $merchantUser->mobile = $mobile;
            $merchantUser->email = $email;
            $merchantUser->password = Hash::make(Str::random(32));
            $merchantUser->user_type = UserType::MERCHANT;
            $merchantUser->status = Status::INACTIVE;
            $merchantUser->verification_status = VerificationStatus::NOT_VERIFIED;
            $merchantUser->document_status = DocumentStatus::NOT_VERIFIED;
            $merchantUser->submit_status = Status::INACTIVE;
            $merchantUser->email_verify_token = Str::random(64);
            $merchantUser->otp = $otp;
            $merchantUser->permissions = [];
            $merchantUser->save();

            $merchant = new Merchant();
            $merchant->user_id = $merchantUser->id;
            $merchant->account_type = 1;
            $merchant->business_name = 'Pending Merchant';
            $merchant->address = null;
            $merchant->merchant_unique_id = $this->repo->generateUniqueID();
            $merchant->cod_charges = [
                'inside_city' => '0',
                'sub_city' => '0',
                'outside_city' => '0',
            ];
            $merchant->opening_balance = 0;
            $merchant->vat = 0;
            $merchant->save();

            $shop = new MerchantShops();
            $shop->merchant_id = $merchant->id;
            $shop->name = $merchant->business_name;
            $shop->contact_no = $mobile;
            $shop->status = Status::ACTIVE;
            $shop->default_shop = Status::ACTIVE;
            $shop->save();

            $deliveryCharges = DeliveryCharge::with('category')->orderBy('position')->get();
            foreach ($deliveryCharges as $delivery) {
                MerchantDeliveryCharge::create([
                    'merchant_id' => $merchant->id,
                    'delivery_charge_id' => $delivery->id,
                    'weight' => $delivery->weight,
                    'category_id' => $delivery->category_id,
                    'same_day' => $delivery->same_day,
                    'next_day' => $delivery->next_day,
                    'sub_city' => $delivery->sub_city,
                    'outside_city' => $delivery->outside_city,
                    'status' => Status::ACTIVE,
                ]);
            }

            session([
                'merchant_signup_user_id' => $merchantUser->id,
                'merchant_signup_contact' => $contact,
                'merchant_signup_type' => $isEmail ? 'email' : 'mobile',
            ]);

            if ($isEmail) {
                try {
                    Mail::to($merchantUser->email)->send(new MerchantSignupVerified([
                        'merchant_user' => $merchantUser,
                        'otp' => $merchantUser->otp,
                    ]));
                } catch (\Throwable $th) {
                }
            } else {
                app(SmsService::class)->sendOtp($merchantUser->mobile, $merchantUser->otp);
            }

            DB::commit();
            return redirect()->route('customer.otp-verification-form');
        } catch (\Throwable $e) {
            DB::rollBack();
            Toastr::error(__('merchant.error_msg'), __('message.error'));
            return redirect()->back()->withInput($request->all());
        }
    }

    public function countryByCity($id)
    {
        $cities = $this->repo->countryByCity($id);
        return response()->json($cities);
    }

    public function cityByDistrict($id)
    {
        $districts = $this->repo->cityByDistrict($id);
        return response()->json($districts);
    }

    public function districtByTown($id)
    {
        $towns = $this->repo->districtByTown($id);
        return response()->json($towns);
    }

    public function townByPortalCode($id)
    {
        $town = $this->repo->townByPortalCode($id);
        return response()->json($town);
    }

    public function townByCity($id)
    {
        $town = $this->repo->townByCity($id);
        return response()->json($town);
    }


    public function otpVerification(OtpRequest $request)
    {
        $signupUserId = session('merchant_signup_user_id');
        if (!$signupUserId) {
            return redirect()->route('customer.sign-up');
        }

        $user = User::where('id', $signupUserId)->where('otp', $request->otp)->first();
        if ($user) {
            $user->otp = null;
            $user->verification_status = Status::ACTIVE;
            // Force both flags so merchant dashboard passes verification gates.
            $user->email_verified_at = now();
            $user->mobile_verified_at = now();
            $user->save();
            session(['merchant_signup_verified' => true]);
            return redirect()->route('customer.complete-profile-form');
        } elseif ($user == null) {
            return redirect()->route('customer.otp-verification-form')->with('warning', 'Invalid OTP');
        } else {
            Toastr::error(__('merchant.error_msg'), __('message.error'));
            return redirect()->back();
        }
    }

    public function otpVerificationForm()
    {
        if (!session()->has('merchant_signup_user_id')) {
            return redirect()->route('customer.sign-up');
        }
        return view('backend.merchant.verification');
    }

    public function resendOTP(Request $request)
    {
        $signupUserId = session('merchant_signup_user_id');
        if (!$signupUserId) {
            return redirect()->route('customer.sign-up');
        }

        $user = User::find($signupUserId);
        if (!$user) {
            return redirect()->route('customer.sign-up');
        }

        $otp = random_int(100000, 999999);
        $user->otp = $otp;
        $user->save();

        if ($user->email) {
            try {
                Mail::to($user->email)->send(new MerchantSignupVerified([
                    'merchant_user' => $user,
                    'otp' => $otp,
                ]));
            } catch (\Throwable $th) {
            }
        } elseif ($user->mobile) {
            app(SmsService::class)->sendOtp($user->mobile, $otp);
        }

        return redirect()->route('customer.otp-verification-form')->with('success', 'Resend OTP');
    }

    public function completeProfileForm()
    {
        if (!session()->has('merchant_signup_user_id') || !session('merchant_signup_verified')) {
            return redirect()->route('customer.sign-up');
        }

        return view('backend.merchant.complete_profile');
    }

    public function completeProfile(Request $request)
    {
        if (!session()->has('merchant_signup_user_id') || !session('merchant_signup_verified')) {
            return redirect()->route('customer.sign-up');
        }

        $request->validate([
            'first_name' => ['required', 'string', 'max:191'],
            'last_name' => ['required', 'string', 'max:191'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        $user = User::find(session('merchant_signup_user_id'));
        if (!$user) {
            return redirect()->route('customer.sign-up');
        }

        $fullName = trim($request->first_name . ' ' . $request->last_name);

        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->name = $fullName;
        $user->password = Hash::make($request->password);
        $user->status = Status::ACTIVE;
        $user->verification_status = Status::ACTIVE;
        $user->document_status = Status::ACTIVE;
        $user->submit_status = Status::ACTIVE;
        $user->save();

        $merchant = Merchant::where('user_id', $user->id)->first();
        if ($merchant) {
            $merchant->business_name = $merchant->business_name === 'Pending Merchant' ? $fullName : $merchant->business_name;
            $merchant->save();
        }

        auth()->login($user);
        session()->forget([
            'merchant_signup_user_id',
            'merchant_signup_contact',
            'merchant_signup_type',
            'merchant_signup_verified',
        ]);

        Toastr::success('Registration completed successfully.', __('message.success'));
        return redirect()->route('merchant-panel.parcel.create');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function view($id)
    {
        $singleMerchant = $this->repo->get($id);
        $merchant_shops = $this->repo->merchant_shops_get($id);
        if (blank($singleMerchant)) {
            abort(404);
        }
        return view('backend.merchant.merchant-details', compact('singleMerchant', 'merchant_shops'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $countries       = $this->repo->all_country();
        $cities       = $this->repo->all_city();
        $districts       = $this->repo->all_district();
        $towns       = $this->repo->all_town();
        $merchant = $this->repo->get($id);
        if (blank($merchant)) {
            abort(404);
        }
        $merchant_payment_dates = MerchantPaymentDate::where('merchant_id', $merchant->id)->get();
        return view('backend.merchant.edit', compact(
            'merchant',
            'countries',
            'cities',
            'districts',
            'towns',
            'merchant_payment_dates'
        ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id, UpdateRequest $request)
    {
        if ($this->repo->update($id, $request)) {
            Toastr::success(__('merchant.update_msg'), __('message.success'));
            return redirect()->route('customer.index');
        } else {
            Toastr::error(__('merchant.error_msg'), __('message.error'));
            return redirect()->back();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if ($this->repo->delete($id)) {
            Toastr::success(__('merchant.delete_msg'), __('message.success'));
            return back();
        } else {
            Toastr::error(__('merchant.error_msg'), __('message.error'));
            return redirect()->back();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function merchantDocumentSubmit(Request $request)
    {
        $merchant = Merchant::findOrFail($request->merchant_id);
        $merchantUser = $merchant->user;

        $request->validate([
            'merchant_id' => ['required', 'exists:merchants,id'],
            'nid' => [$merchant->nid_id ? 'nullable' : 'required', 'image', 'mimes:jpeg,png,jpg', 'max:5098'],
            'nid_back' => [$merchant->nid_back_id ? 'nullable' : 'required', 'image', 'mimes:jpeg,png,jpg', 'max:5098'],
            'image_id' => [($merchantUser && $merchantUser->image_id) ? 'nullable' : 'required', 'image', 'mimes:jpeg,png,jpg', 'max:5098'],
            'trade_license' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:5098'],
        ]);

        if ($this->repo->merchantDocumentSubmit($request)) {
            Toastr::success(__('merchant.update_msg'), __('message.success'));
            return redirect()->back();
        } else {
            Toastr::error(__('merchant.error_msg'), __('message.error'));
            return redirect()->back();
        }
    }

    public function accountStatus($id)
    {
        if ($this->repo->accountStatus($id)) {
            Toastr::success(__('merchant.update_msg'), __('message.success'));
            return redirect()->back();
        } else {
            Toastr::error(__('merchant.error_msg'), __('message.error'));
            return redirect()->back();
        }
    }

    public function verificationStatus($id)
    {
        if ($this->repo->verificationStatus($id)) {
            Toastr::success(__('merchant.update_msg'), __('message.success'));
            return redirect()->back();
        } else {
            Toastr::error(__('merchant.error_msg'), __('message.error'));
            return redirect()->back();
        }
    }

    public function documentStatus($id)
    {
        if ($this->repo->documentStatus($id)) {
            Toastr::success(__('merchant.update_msg'), __('message.success'));
            return redirect()->back();
        } else {
            Toastr::error(__('merchant.error_msg'), __('message.error'));
            return redirect()->back();
        }
    }

    public function getProvinceCity(Request $request)
    {
        return City::where('province_id', $request->id)->get();
    }
}
