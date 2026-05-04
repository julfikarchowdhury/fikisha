<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Services\SmsService;
use App\Mail\MerchantSignupVerified;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class VerificationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Email Verification Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling email verification for any
    | user that recently registered with the application. Emails may also
    | be re-sent if the user didn't receive the original email message.
    |
    */

    use VerifiesEmails;

    /**
     * Where to redirect users after verification.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }

    public function emailVerify($token)
    {
        $user = User::where('email_verify_token', $token)->first();
        if (!empty($user)) {
            try {
                DB::beginTransaction();
                $user->email_verified_at = now();
                $user->email_verify_token = null;
                $user->save();
                DB::commit();
                Toastr::success(__('merchant.email_verify_success'), __('message.success'));
                return redirect('/dashboard');
            } catch (\Exception $e) {
                DB::rollBack();
                Toastr::error(__('merchant.error_msg'), __('message.error'));
                return redirect()->back();
            }
        } else {
            Toastr::error(__('merchant.invalid_token'), __('message.error'));
            return redirect('/dashboard');
        }
    }

    public function resendEmailVerify()
    {
        $user = User::find(auth()->id());
        $token = Str::random(64);
        if (!empty($user)) {
            try {
                DB::beginTransaction();
                $user->email_verify_token = $token;
                $user->save();
                $data = [
                    'merchant_user' => $user
                ];
                Mail::to($user->email)->send(new MerchantSignupVerified($data));
                DB::commit();
                Toastr::success(__('merchant.resend_email'), __('message.success'));
                return redirect('/dashboard');
            } catch (\Exception $e) {
                DB::rollBack();
                Toastr::error(__('merchant.error_msg'), __('message.error'));
                return redirect()->back();
            }
        } else {
            Toastr::error(__('merchant.unauthorized_user'), __('message.error'));
            return redirect('/dashboard');
        }
    }

    public function phoneVerify()
    {
        $user = User::find(auth()->id());
        $otp  = random_int(10000, 99999);
        if (!empty($user)) {
            try {
                DB::beginTransaction();
                $user->otp = $otp;
                $user->save();
                app(SmsService::class)->sendOtp($user->mobile, $user->otp);
                DB::commit();
                Toastr::success('Resend OTP your phone', __('message.success'));
                return redirect()->route('verify.phone');
            } catch (\Exception $e) {
                DB::rollBack();
                Toastr::error(__('merchant.error_msg'), __('message.error'));
                return redirect()->back();
            }
        } else {
            Toastr::error(__('merchant.unauthorized_user'), __('message.error'));
            return redirect('/dashboard');
        }
    }

    public function verifyPhone()
    {
        $user = User::find(auth()->id());
        if (!empty($user)) {
            return view('backend.merchant.phone_verification', compact('user'));
        } else {
        
            Toastr::error(__('merchant.unauthorized_user'), __('message.error'));
            return redirect('/dashboard');
        }
    }

    public function merchantResendOtp(Request $request)
    {
        $user = User::find(auth()->id());
        $otp  = random_int(10000, 99999);
        if (!empty($user)) {
            try {
                DB::beginTransaction();
                $user->otp = $otp;
                $user->save();
                app(SmsService::class)->sendOtp($user->mobile, $user->otp);
                DB::commit();
                Toastr::success('Resend OTP your phone', __('message.success'));
                return redirect()->route('verify.phone');
            } catch (\Exception $e) {
                DB::rollBack();
                Toastr::error(__('merchant.error_msg'), __('message.error'));
                return redirect()->back()->withInput($request->all());
            }
        } else {
            Toastr::error(__('merchant.unauthorized_user'), __('message.error'));
            return redirect()->route('verify.phone');
        }
    }

    public function merchantVerifyPhone(Request $request)
    {
 
        $user = User::where('otp', $request->otp)->find(auth()->id());
   
        if (!empty($user)) {
            try {
                DB::beginTransaction(); 
                $user->mobile_verified_at = now();
                $user->otp = null;
                $user->save();
                DB::commit();
                Toastr::success(__('merchant.phone_verify_success'), __('message.success'));
                return redirect('/dashboard');
            } catch (\Exception $e) {
                DB::rollBack();
                Toastr::error(__('merchant.error_msg'), __('message.error'));
                return redirect()->back()->withInput($request->all());
            }
        } else {
           
            Toastr::error(__('merchant.invalid_otp'), __('message.error'));
            return redirect()->back();
        }
    }
}
