<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Backend\MerchantOnlinePayment;
use App\Repositories\PayoutSetup\PayoutSetupInterface;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;

class PayoutSetupController extends Controller
{

    protected $repo, $MOPrepo, $MOPmodel;
    public function __construct(PayoutSetupInterface $repo, MerchantOnlinePayment $MOPmodel)
    {
        $this->repo     = $repo;
        $this->MOPmodel  = $MOPmodel;
    }

    public function index()
    {
        return view('backend.setting.payout_setup.index');
    }

    public function PayoutSetupUpdate(Request $request, $paymentMethod)
    {

        if ($this->repo->update($paymentMethod, $request)) :
            Toastr::success(__('menus.payout_setup_updated'), __('message.success'));
            return redirect()->back();
        else :
            Toastr::error(__('parcel.error_msg'), __('message.error'));
            return redirect()->back();
        endif;
    }

    public function onlinePaymentList()
    {
        $payments =  $this->MOPmodel::orderByDesc('id')->paginate(10);
        return view('backend.online_payment.online_payment_list', compact('payments'));
    }

    public function testMpesaToken()
    {
        $consumerKey = globalSettings('mpesa_consumer_key');
        $consumerSecret = globalSettings('mpesa_consumer_secret');
        $environment = globalSettings('mpesa_environment') ?: 'sandbox';

        if (!$consumerKey || !$consumerSecret) {
            $message = 'M-Pesa consumer key/secret are missing.';
            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }
            Toastr::error($message, __('message.error'));
            return redirect()->back();
        }

        $baseUrl = $environment === 'live' ? 'https://api.safaricom.co.ke' : 'https://sandbox.safaricom.co.ke';

        $tokenResponse = \Illuminate\Support\Facades\Http::withBasicAuth($consumerKey, $consumerSecret)
            ->timeout(15)
            ->connectTimeout(10)
            ->get($baseUrl . '/oauth/v1/generate', ['grant_type' => 'client_credentials']);

        if (!$tokenResponse->ok()) {
            \Illuminate\Support\Facades\Log::error('M-Pesa token test failed', [
                'status' => $tokenResponse->status(),
                'body' => $tokenResponse->body(),
            ]);
            $message = 'M-Pesa token test failed. Check credentials.';
            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }
            Toastr::error($message, __('message.error'));
            return redirect()->back();
        }

        $accessToken = $tokenResponse->json('access_token');
        if (!$accessToken) {
            $message = 'M-Pesa token test failed. No token returned.';
            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }
            Toastr::error($message, __('message.error'));
            return redirect()->back();
        }

        $message = 'M-Pesa token is valid.';
        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => $message]);
        }
        Toastr::success($message, __('message.success'));
        return redirect()->back();
    }
}
