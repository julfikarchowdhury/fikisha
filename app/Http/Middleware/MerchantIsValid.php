<?php

namespace App\Http\Middleware;

use App\Enums\UserType;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;

class MerchantIsValid
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(Auth::user()->user_type == UserType::DELIVERYMAN):
            return $next($request);
        endif; 
        if (empty(auth()->user()->email_verified_at) && empty(auth()->user()->mobile_verified_at) && auth()->user()->status == 1) {
            Toastr::error(__('merchant.unauthorized_permission'), __('message.error'));
            return redirect('/dashboard');
        }
        return $next($request);
    }
}
