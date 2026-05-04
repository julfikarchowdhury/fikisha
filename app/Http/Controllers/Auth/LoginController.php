<?php

namespace App\Http\Controllers\Auth;

use App\Enums\Status;
use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Http;
use Brian2694\Toastr\Facades\Toastr;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    // Auth login
    public function login(Request $request)
    {
        $this->validateLogin($request);
        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if (method_exists($this, 'hasTooManyLoginAttempts') && $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            if ($request->hasSession()) {
                $request->session()->put('auth.password_confirmed_at', time());
            }
            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);
        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Where to redirect users after login.
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
        $this->middleware('guest')->except('logout');
    }

    protected function credentials(Request $request)
    {
        if (is_numeric($request->get('email'))) {
            return ['mobile' => $request->get('email'), 'password' => $request->get('password')];
        }
        return ['email' => $request->get('email'), 'password' => $request->get('password')];
    }

    protected function authenticated(Request $request, $user)
    {
        if ((int) $user->user_type === UserType::DELIVERYMAN) {
            Auth::logout();
            Toastr::error('Deliveryman accounts cannot login from this panel. Please use rider app.', __('message.error'));
            return redirect()->route('login');
        }

        if ($user->user_type != UserType::MERCHANT) {
            if ($user->user_type == Status::INACTIVE) {
                Auth::logout();
                Toastr::error(__('auth.status_failed'), __('message.error'));
                return redirect()->route('login');
            } elseif ($user->verification_status == Status::INACTIVE) {
                Auth::logout();
                Toastr::error(__('auth.verification_status_failed'), __('message.error'));
                return redirect()->route('login');
            }
            return redirect()->route('dashboard.index');
        }
        return redirect()->route('dashboard.index');
    }
}
