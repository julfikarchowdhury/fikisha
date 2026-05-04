<?php

use App\Http\Controllers\Api\V10\AccountTransactionController;
use App\Http\Controllers\Api\V10\AnalyticsController;
use App\Http\Controllers\Api\V10\DashboardController;
use App\Http\Controllers\Api\V10\DeliveryManIncomeExpenseController;
use App\Http\Controllers\Api\V10\DeliverymanOrderController;
use App\Http\Controllers\Api\V10\DeliveryManParcelController;
use App\Http\Controllers\Api\V10\FraudController;
use App\Http\Controllers\Api\V10\NewsOfferController;
use App\Http\Controllers\Api\V10\ParcelController;
use App\Http\Controllers\Api\V10\PaymentAccountController;
use App\Http\Controllers\Api\V10\PaymentRequestController;
use App\Http\Controllers\Api\V10\PushNotificationController;
use App\Http\Controllers\Api\V10\SettingsController;
use App\Http\Controllers\Api\V10\ShopsController;
use App\Http\Controllers\Api\V10\StatementsController;
use App\Http\Controllers\Api\V10\SupportController;
use App\Http\Controllers\Backend\MerchantPanel\MpesaController as MerchantPanelMpesaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V10\AuthController;
use App\Http\Controllers\Api\V10\DeliverymanController;
use App\Http\Controllers\Api\V10\FrontendController;
use App\Http\Controllers\Api\V10\GeneralSettingCotroller;
use App\Http\Controllers\Api\V10\InvoiceController;
use App\Http\Controllers\Api\V10\ReportController;
use App\Http\Controllers\Api\Rider\MarketplaceParcelController;
use App\Http\Controllers\Api\Rider\RiderAuthController;
use App\Http\Controllers\Api\Rider\ParcelDisputeController as RiderParcelDisputeController;
use App\Http\Controllers\Api\Rider\RiderLocationController;
use App\Http\Controllers\Api\TrackingController;
use App\Http\Controllers\Api\Merchant\AuthController as MerchantAuthController;
use App\Http\Controllers\Api\Merchant\LocationController as MerchantLocationController;
use App\Http\Controllers\Api\Merchant\ParcelController as MerchantParcelController;
use App\Http\Controllers\Api\Merchant\PaymentController as MerchantPaymentController;
use App\Http\Controllers\Api\Merchant\ParcelQuoteController as MerchantParcelQuoteController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('mpesa/callback', [MerchantPanelMpesaController::class, 'callback'])->name('mpesa.callback');

Route::prefix('rider')->group(function () {
    Route::get('/config', [RiderAuthController::class, 'config']);
    Route::post('/signup/request-otp', [RiderAuthController::class, 'signupRequestOtp']);
    Route::post('/signup/verify-otp', [RiderAuthController::class, 'signupVerifyOtp']);
    Route::post('/signup/complete', [RiderAuthController::class, 'signupComplete']);
    Route::get('/states', [RiderAuthController::class, 'states']);
    Route::get('/cities', [RiderAuthController::class, 'citiesByState']);
    Route::post('/register', [RiderAuthController::class, 'register']);
    Route::post('/verify-phone', [RiderAuthController::class, 'verifyPhone']);
    Route::post('/verify-identifier', [RiderAuthController::class, 'verifyIdentifier']);
    Route::post('/login', [RiderAuthController::class, 'login']);
    Route::post('/forgot-password/request-otp', [RiderAuthController::class, 'requestForgotPasswordOtp']);
    Route::post('/forgot-password/verify-otp', [RiderAuthController::class, 'verifyForgotPasswordOtp']);
    Route::post('/forgot-password/reset', [RiderAuthController::class, 'resetForgotPassword']);

    Route::middleware(['auth:sanctum', 'riderAuth'])->group(function () {
        Route::post('/kyc-upload', [RiderAuthController::class, 'kycUpload']);
        Route::post('/logout', [RiderAuthController::class, 'logout']);
        Route::get('/status', [RiderAuthController::class, 'status']);
        Route::get('/profile', [RiderAuthController::class, 'profile']);
        Route::post('/profile', [RiderAuthController::class, 'updateProfile']);
        Route::post('/device-token', [RiderAuthController::class, 'deviceToken']);
    });

    Route::middleware(['auth:sanctum', 'riderAuth', 'riderApproved'])->group(function () {
        Route::get('/parcels/available', [MarketplaceParcelController::class, 'index']);
        Route::get('/parcels/list', [MarketplaceParcelController::class, 'listByStatus']);
        Route::get('/parcels/active', [MarketplaceParcelController::class, 'active']);
        Route::get('/parcels/delivered', [MarketplaceParcelController::class, 'delivered']);
        Route::get('/parcels/{parcel}', [MarketplaceParcelController::class, 'show']);
        Route::post('/parcels/{parcel}/accept', [MarketplaceParcelController::class, 'accept']);
        Route::post('/parcels/{parcel}/cancel', [MarketplaceParcelController::class, 'cancel']);
        Route::post('/parcels/{parcel}/send-pickup-otp', [MarketplaceParcelController::class, 'sendPickupOtp']);
        Route::post('/parcels/{parcel}/verify-pickup-otp', [MarketplaceParcelController::class, 'verifyPickupOtp']);
        Route::post('/parcels/{parcel}/send-otp', [MarketplaceParcelController::class, 'sendOtp']);
        Route::post('/parcels/{parcel}/verify-otp', [MarketplaceParcelController::class, 'verifyOtp']);
        Route::post('/parcels/{parcel}/status', [MarketplaceParcelController::class, 'status']);
        Route::post('/parcels/{id}/raise-dispute', [RiderParcelDisputeController::class, 'raise']);
        Route::get('/disputes/reasons', [RiderParcelDisputeController::class, 'reasons']);
        Route::get('/disputes', [RiderParcelDisputeController::class, 'list']);
        Route::get('/wallet', [\App\Http\Controllers\Api\Rider\RiderWalletController::class, 'summary']);
        Route::get('/wallet/transactions', [\App\Http\Controllers\Api\Rider\RiderWalletController::class, 'transactions']);
        Route::get('/earnings', [\App\Http\Controllers\Api\Rider\RiderWalletController::class, 'earnings']);
        Route::post('/withdraw-request', [\App\Http\Controllers\Api\Rider\RiderWalletController::class, 'requestWithdrawal']);
        Route::get('/withdraw-requests', [\App\Http\Controllers\Api\Rider\RiderWalletController::class, 'listRequests']);
        Route::post('/location', [RiderLocationController::class, 'store']);
        Route::post('/toggle-availability', [RiderAuthController::class, 'toggleAvailability']);
    });
});

Route::prefix('merchant')->group(function () {
    Route::get('/config', [MerchantAuthController::class, 'config']);
    Route::get('/states', [MerchantLocationController::class, 'states']);
    Route::get('/cities', [MerchantLocationController::class, 'cities']);
    Route::post('/register/request-otp', [MerchantAuthController::class, 'requestOtp']);
    Route::post('/register/verify-otp', [MerchantAuthController::class, 'verifyOtp']);
    Route::post('/register', [MerchantAuthController::class, 'register']);
    Route::post('/login', [MerchantAuthController::class, 'login']);
    Route::post('/forgot-password/request-otp', [MerchantAuthController::class, 'requestForgotPasswordOtp']);
    Route::post('/forgot-password/verify-otp', [MerchantAuthController::class, 'verifyForgotPasswordOtp']);
    Route::post('/forgot-password/reset', [MerchantAuthController::class, 'resetForgotPassword']);

    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('/profile', [MerchantAuthController::class, 'profile']);
        Route::patch('/profile', [MerchantAuthController::class, 'updateProfile']);
        Route::post('/logout', [MerchantAuthController::class, 'logout']);
        Route::get('/payments/history', [MerchantPaymentController::class, 'history']);
        Route::post('/payments/mpesa/prompt', [MerchantPaymentController::class, 'prompt']);
        Route::post('/parcels/quote', [MerchantParcelQuoteController::class, 'quote']);
        Route::get('/parcels', [MerchantParcelController::class, 'index']);
        Route::get('/parcels/{parcel}/tracking', [MerchantParcelController::class, 'tracking']);
        Route::post('/parcels', [MerchantParcelController::class, 'store']);
        Route::post('/parcels/{parcel}/pay', [MerchantPaymentController::class, 'pay']);
        Route::patch('/parcels/{parcel}/payer', [MerchantPaymentController::class, 'updatePayer']);
    });
});

Route::get('/tracking/{tracking_token}', [TrackingController::class, 'show']);

Route::prefix('v10')->group(function () {

    Route::middleware(['CheckApiKey'])->group(function () {

        // all apis goes here
        Route::post('/register',                                        [AuthController::class, 'register']);
        Route::post('/signin',                                          [AuthController::class, 'signin']);
        Route::post('/deliveryman/login',                               [AuthController::class, 'deliveryManLogin']);
        Route::post('/otp-verification',                                [AuthController::class, 'otpVerification']);
        Route::post('/resend-otp',                                      [AuthController::class, 'resendOTP']);
        Route::post('/password/email',                                  [AuthController::class, 'sendPasswordResetLinkEmail'])->middleware('throttle:5,1');
        Route::post('/password/reset',                                  [AuthController::class, 'resetPassword']);
        //general settings api
        Route::get('/general-settings',                                 [GeneralSettingCotroller::class, 'index']);
        Route::get('/all-currencies',                                   [GeneralSettingCotroller::class, 'currencies']);

        Route::group(['middleware' => ['auth:sanctum']], function () {
            Route::get('/refresh',                                      [AuthController::class, 'refresh']);
            Route::get('/dashboard',                                    [DashboardController::class, 'index']);
            Route::get('/dashboard/filter',                             [DashboardController::class, 'filter']);
            Route::get('/profile',                                      [AuthController::class, 'profile']);
            Route::post('/profile/update',                              [AuthController::class, 'profileUpdate']);
            //push notification
            Route::post('fcm-subscribe',                                [PushNotificationController::class, 'fcmSubscribe']);
            Route::post('fcm-unsubscribe',                              [PushNotificationController::class, 'fcmUnsubscribe']);

            Route::post('/update-password',                              [AuthController::class, 'updatePassword']);
            Route::post('/sign-out',                                    [AuthController::class, 'logout']);
            //deliveryman
            Route::prefix('deliveryman')->group(function(){
                Route::get('/dashboard',                         [DeliverymanController::class, 'dashboard']);
                Route::get('/profile',                           [DeliverymanController::class, 'profile']);
                Route::post('/profile/update',                   [DeliverymanController::class, 'profileUpdate']);
                Route::get('/parcel/index',                      [DeliveryManParcelController::class, 'index']);
                Route::get('/parcel/details/{id}',               [DeliveryManParcelController::class, 'details']);
                Route::post('/parcel/delivered/{id}',            [DeliveryManParcelController::class, 'parcelDelivered']);
                Route::post('/parcel/partial-delivered/{id}',    [DeliveryManParcelController::class, 'parcelPartialDelivered']);
                Route::get('/income-expense',                    [DeliveryManIncomeExpenseController::class, 'deliverymanIncomeExpense']);
                Route::get('/payment-logs',                      [DeliverymanController::class, 'paymentLogs']);
                Route::get('/parcel-payment-logs',               [DeliverymanController::class, 'parcelPaymentLogs']);
                Route::get('/parcel-status',                     [DeliverymanController::class, 'parcelStatus']);
                Route::get('/parcel-status-update',              [DeliverymanController::class, 'parcelStatusUpdate']);

                Route::get('/order/list',                        [DeliverymanOrderController::class, 'orderList']);
                Route::get('/order/search',                     [DeliverymanOrderController::class, 'orderSearch']);
                Route::get('/order/deliverymanAssign',          [DeliverymanOrderController::class, 'orderDeliverymanAssign']);
                Route::get('/order/status/show',                 [DeliverymanOrderController::class, 'orderStatus']);
                Route::get('/order/status/update',               [DeliverymanOrderController::class, 'parcelStatusUpdate']);
                Route::get('/order/details/{id}',                [DeliverymanOrderController::class, 'details']);

            });


        });

        // Website Api
        Route::controller(FrontendController::class)->group(function () {
            Route::get('/change/language/{id}', 'changeLanguage');
            Route::post('/merchant_sign_up', 'merchantSignUp');
            Route::get('/packaging_list', 'packagingList');

            Route::group(['middleware' => ['auth:sanctum']], function () {
                Route::post('/get_shipping_type', 'getShippingType');
                Route::post('/calculate_the_quote', 'calculateTheQuote');
                Route::post('/parcel_item_calculate', 'parcelItemCalculate');
                Route::post('/send_parcels', 'sendParcels');
            });

            Route::post('/track_my_parcel', 'trackMyParcel');
            Route::get('/services', 'services');
            Route::get('/province_list', 'provinceList');
            Route::post('/our_network', 'ourNetwork');


            Route::prefix('pages')->group(function(){
                Route::get('legal-links',       'legalPageLinks');
                Route::get('contact',           'contactUs');
                Route::post('/contact/message/send', 'contactMessageSend');
                Route::get('privacy-policy',    'privacyPolicy');
                Route::get('terms-conditions',  'termsConditions');
                Route::get('aboutus',           'aboutUs');
                Route::get('faq',               'getFaq');
                Route::get('network-coverage',  'networkCoverage');
                Route::get('track-shippment',   'trackshippment');
                Route::get('booking',   'booking');
            });
            Route::get('sections',               'sections');
            Route::get('service-list',           'serviceList');
            Route::get('service-details/{id}',   'serviceDetails');
            Route::get('blog-list',              'blogList');
            Route::get('blog-details/{id}',      'BlogDetails');
            Route::get('partner',                'partner');
            Route::get('social-links',           'socialLinks');
            Route::get('get/sliders',            'sliders');
            Route::get('default/data',           'defaultData');

        });

    });

    //frontend api
    Route::get('parcel/tracking/{tracking_id}',                         [ParcelController::class, 'parcelTrackingLogs']);
    Route::post('/contact-us',                                          [ParcelController::class, 'ContactUs']);
    Route::post('/subscribe',                                           [ParcelController::class, 'subscribe']);
    Route::get('/delivery-charges',                                     [ParcelController::class, 'DeliveryCharges']);
});
