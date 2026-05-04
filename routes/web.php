<?php
/*
|--------------------------------------------------------------------------
| Laravel WeDelivery App
|--------------------------------------------------------------------------
|
| Autor Name : WemaxDevs
| Website : www.wemaxdevs.com
| Email : info@wemaxdevs.com
|
*/

use App\Http\Controllers\AamarpayController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Backend\AddonController;
use App\Http\Controllers\Backend\DatabaseBackupController;
use App\Http\Controllers\Backend\NotificationSettingsController;
use App\Http\Controllers\Backend\PushNotificationController;
use App\Http\Controllers\Backend\TotalSummeryReportController;
use App\Http\Controllers\Backend\RiderWithdrawRequestController;
use App\Http\Controllers\Backend\RiderWalletController;

use App\Http\Controllers\Backend\MerchantPanel\MerchantParcelController;
use App\Http\Controllers\Backend\MerchantPanel\ParcelDisputeController as MerchantParcelDisputeController;
use App\Http\Controllers\Backend\MerchantPanel\StatementsController;
use App\Http\Controllers\Backend\SmsSendSettingsController;
use App\Http\Controllers\Backend\SmsSettingsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\Backend\MerchantDeliveryChargeController;
use App\Http\Controllers\Backend\ProfileController;
use App\Http\Controllers\Backend\MerchantProfileController;
use App\Http\Controllers\Backend\MerchantController;
use App\Http\Controllers\Backend\MailSettingsController;
use App\Http\Controllers\Backend\ParcelController;
use App\Http\Controllers\Backend\ParcelDisputeController as AdminParcelDisputeController;
use App\Http\Controllers\Backend\PlatformLedgerController;
use App\Http\Controllers\Backend\DeliveryChargeController;
use App\Http\Controllers\Backend\MerchantShopsController;
use App\Http\Controllers\Backend\PackagingController;
use App\Http\Controllers\Backend\RoleController;
use App\Http\Controllers\Backend\ActiveLogController;
use App\Http\Controllers\Backend\UserController;
use App\Http\Controllers\Backend\DeliveryManController;
use App\Http\Controllers\Backend\DesignationController;
use App\Http\Controllers\Backend\DepartmentController;
use App\Http\Controllers\Backend\FraudController;
use App\Http\Controllers\Backend\AccountController;
use App\Http\Controllers\Backend\AccountHeadsController;
use App\Http\Controllers\Backend\AdminAamarpayController;
use App\Http\Controllers\Backend\AdminBkashController;
use App\Http\Controllers\Backend\AdminSkrillController;
use App\Http\Controllers\Backend\AdminSslCommerzController;
use App\Http\Controllers\Backend\DeliveryTypeController;
use App\Http\Controllers\MerchantmanagePaymentController;
use App\Http\Controllers\Backend\FundTransferController;
use App\Http\Controllers\Backend\IncomeController;
use App\Http\Controllers\Backend\BankTransactionController;
use App\Http\Controllers\Backend\LiquidFragileController;
use App\Http\Controllers\Backend\ExpenseController;
use App\Http\Controllers\MerchantPaymentAccountController;
use App\Http\Controllers\Backend\TodoController;
use App\Http\Controllers\Backend\SupportController;
use App\Http\Controllers\Backend\GeneralSettingsController;
use App\Http\Controllers\Backend\AssetcategoryController;
use App\Http\Controllers\Backend\NewsOfferController;
use App\Http\Controllers\Backend\SalaryController;
use App\Http\Controllers\Backend\AssetController;
use App\Http\Controllers\Backend\BkashController;
use App\Http\Controllers\Backend\CityController;
use App\Http\Controllers\Backend\CurrencyController;
use App\Http\Controllers\Backend\CountryController;
use App\Http\Controllers\Backend\DistrictController;
use App\Http\Controllers\Backend\ExtraCostController;
use App\Http\Controllers\Backend\FrontWeb\BlogController;
use App\Http\Controllers\Backend\FrontWeb\FaqController;
use App\Http\Controllers\Backend\FrontWeb\PageController;
use App\Http\Controllers\Backend\FrontWeb\PartnerController;
use App\Http\Controllers\Backend\FrontWeb\SectionController;
use App\Http\Controllers\Backend\FrontWeb\ServiceController;
use App\Http\Controllers\Backend\FrontWeb\SliderController;
use App\Http\Controllers\Backend\FrontWeb\SocialLinkController;
use App\Http\Controllers\Backend\FrontWeb\WhyCourierController;
use App\Http\Controllers\Backend\HRM\ApplyLeaveController;
use App\Http\Controllers\Backend\HRM\AttendanceController;
use App\Http\Controllers\Backend\HRM\DutyScheduleController;
use App\Http\Controllers\Backend\HRM\HolidayController;
use App\Http\Controllers\Backend\HRM\LeaveAssignController;
use App\Http\Controllers\Backend\HRM\LeaveController;
use App\Http\Controllers\Backend\HRM\LeaveTypeController;
use App\Http\Controllers\Backend\HRM\WeekendController;
use App\Http\Controllers\Backend\MerchantInvoiceController;
//merchant panel controller
use App\Http\Controllers\Backend\MerchantPanel\SettingsController;
use App\Http\Controllers\Backend\MerchantPanel\MpesaController as MerchantPanelMpesaController;
use App\Http\Controllers\Backend\MerchantPanel\MpesaPaymentHistoryController;
use App\Http\Controllers\Backend\MerchantPanel\PaymentAccountController;
use App\Http\Controllers\Backend\MerchantPanel\AccountTransactionController;
use App\Http\Controllers\Backend\MerchantPanel\PaymentRequestController;
use App\Http\Controllers\Backend\MerchantPanel\ShopsController;
use App\Http\Controllers\Backend\MerchantPanel\NewsOfferController as MerchantNewsOfferController;
use App\Http\Controllers\Backend\MerchantPanel\SupportController as MerchantPanelSupportController;
use App\Http\Controllers\Backend\MerchantPanel\FraudController as MerchantPanelFraudController;
use App\Http\Controllers\Backend\MerchantPanel\InvoiceController;
use App\Http\Controllers\Backend\MerchantPanel\MerchantOnlinePaymentSetupController;
use App\Http\Controllers\Backend\MerchantPanel\ReportsController as MerchantPanelReportsController;
use App\Http\Controllers\Backend\MerchantPanel\MerchantReportsController;
use App\Http\Controllers\Backend\MerchantPanel\OnlinePaymentController;
use App\Http\Controllers\Backend\MerchantPanel\PickupRequestController as MerchantPanelPickupRequestController;
use App\Http\Controllers\Backend\MerchantPanel\SenderPanelCustomerController;
use App\Http\Controllers\Backend\MerchantPanel\WalletController;
use App\Http\Controllers\Backend\ParcelCategoryController;

use App\Http\Controllers\Backend\PayoutController;
use App\Http\Controllers\Backend\PayoutSetupController;
use App\Http\Controllers\Backend\PickupRequestController;
use App\Http\Controllers\Backend\ProvinceController;
use App\Http\Controllers\Backend\ReportsController;
use App\Http\Controllers\Backend\SalaryGenerateController;
use App\Http\Controllers\Backend\SenderCustomerController;
use App\Http\Controllers\Backend\ShippingTypeController;
use App\Http\Controllers\Backend\SkrillController;
use App\Http\Controllers\Backend\SocialLoginController;
use App\Http\Controllers\Backend\SslCommerzPaymentController;
use App\Http\Controllers\Backend\StatisticsController;
use App\Http\Controllers\Backend\TownController;
use App\Http\Controllers\Backend\VehicleController;
use App\Http\Controllers\LocalizationController;
use App\Http\Controllers\Backend\WebNotificationController;
use App\Http\Controllers\Backend\ZoneController;
use App\Http\Controllers\Frontend\FrontendController;
use App\Http\Controllers\InstallerController;
use App\Http\Controllers\TrackingPageController;
use App\Http\Services\PushNotificationService;
use Illuminate\Support\Facades\Auth;

//installer
Route::middleware(['XSS', 'IsNotInstalled'])->group(function () {
    Route::get('install',                     [InstallerController::class, 'index']);
});

Route::middleware(['XSS'])->group(function () {
    Route::post('installing',                      [InstallerController::class, 'installing'])->name('installing');
    Route::get('finish',                           [InstallerController::class, 'finish'])->name('final');
});

//end installer
Route::middleware(['XSS', 'IsInstalled'])->group(function () {
    Auth::routes();



    Route::get('customer/sign-up',                [MerchantController::class, 'signUp'])->name('customer.sign-up');
    Route::get('country/by/city/{id}',            [MerchantController::class, 'countryByCity']);
    Route::get('city/by/district/{id}',           [MerchantController::class, 'cityByDistrict']);
    Route::get('district/by/town/{id}',           [MerchantController::class, 'districtByTown']);
    Route::get('town/by/portal_code/{id}',        [MerchantController::class, 'townByPortalCode']);
    Route::get('town/by/city/{id}',               [MerchantController::class, 'townByCity']);
    Route::post('customer/sign-up-store',         [MerchantController::class, 'signUpStore'])->name('customer.sign-up-store');
    Route::post('customer/otp-verification',      [MerchantController::class, 'otpVerification'])->name('customer.otp-verification');
    Route::get('customer/otp-verification-form',  [MerchantController::class, 'otpVerificationForm'])->name('customer.otp-verification-form');
    Route::post('customer/resend-otp',            [MerchantController::class, 'resendOTP'])->name('customer.resend-otp');
    Route::get('customer/complete-profile',       [MerchantController::class, 'completeProfileForm'])->name('customer.complete-profile-form');
    Route::post('customer/complete-profile',      [MerchantController::class, 'completeProfile'])->name('customer.complete-profile');
    Route::post('get/province/city',              [MerchantController::class, 'getProvinceCity'])->name('get.province.city');
    Route::get('localization/{language}', [LocalizationController::class, 'setLocalization'])->name('setlocalization');
    Route::get('tracking', [FrontendController::class, 'tracking'])->name('tracking.index');
    Route::get('track/{tracking_token}', [TrackingPageController::class, 'show'])->name('track.public');

    //get qoute
    Route::post('city/wise/portal_code',      [TownController::class, 'cityWisePortalCode'])->name('city.wise.portal_code');
    Route::post('get-qoute/parcel/delivery-charge/unit/parcel',   [MerchantParcelController::class, 'deliveryChargeUnitParcel'])->name('merchant-panel.getqoute.parcel.deliveryCharge.unitParcel.get');
    Route::post('get-qoute/parcel/delivery-charge',   [MerchantParcelController::class, 'deliveryCharge'])->name('merchant-panel.getqoute.parcel.deliveryCharge.get');
    Route::post('getquote/parcel/merchant',           [ParcelController::class, 'getMerchant'])->name('parcel.getquote.merchant.get');
    Route::post('getqoute/get-receiver-suggestions',          [ParcelController::class, 'receiverSuggestions'])->name('getqoute.get.receiver.suggestions');
    Route::post('getqoute/get/province/account/type/wise/merchant',     [ParcelController::class, 'getProvinceAndAccountTypeWiseMerchant'])->name('getqoute.get.province.account_type.wise.merchant');

    Route::post('get-qoute/deliverycharge/tocountries',  [ParcelController::class, 'toCountries']);
    Route::post('get-qoute/deliverycharge/tocities',     [ParcelController::class, 'toCities']);
    Route::post('get-qoute/deliverycharge/todistrict',   [ParcelController::class, 'toDistrict']);
    Route::post('get-qoute/deliverycharge/totown',       [ParcelController::class, 'toTown']);
    Route::post('get-qoute/deliverycharge/toportalcode', [ParcelController::class, 'toPortalCode']);
    Route::get('get-qoute/get-shipping-type',            [ShippingTypeController::class,  'getShippingType'])->name('get-qoute.get.shipping.type');
    Route::get('get-qoute/parcel/add-item',              [ParcelController::class, 'addItem'])->name('get-qoute.parcel.add.item');
    //end get qoute

    Route::group(['middleware' => 'auth'], function () {
        // XSS Protection
        Route::group(['middleware' => 'XSS'], function () {
            //Admin Dashboard Controller
            Route::get('/dashboard',             [DashboardController::class, 'index'])->name('dashboard.index');

            Route::post('search-charts',         [DashboardController::class, 'searchCharts'])->name('search-charts');
            //Admin Category Controller
            Route::get('category/index',         [CategoryController::class, 'index'])->name('category.index')->middleware('hasPermission:category_read');
            Route::get('category/create',        [CategoryController::class, 'create'])->name('category.create')->middleware('hasPermission:category_create');
            Route::post('category/store',        [CategoryController::class, 'store'])->name('category.store')->middleware('hasPermission:category_create');
            Route::get('category/edit/{id}',     [CategoryController::class, 'edit'])->name('category.edit')->middleware('hasPermission:category_update');
            Route::put('category/update',        [CategoryController::class, 'update'])->name('category.update')->middleware('hasPermission:category_update');
            Route::delete('category/delete/{id}', [CategoryController::class, 'destroy'])->name('category.delete')->middleware('hasPermission:category_delete');

            Route::post('deliverycharge/tocountries',  [ParcelController::class, 'toCountries']);
            Route::post('deliverycharge/tocities',     [ParcelController::class, 'toCities']);
            Route::post('deliverycharge/todistrict',   [ParcelController::class, 'toDistrict']);
            Route::post('deliverycharge/totown',       [ParcelController::class, 'toTown']);
            Route::post('deliverycharge/toportalcode', [ParcelController::class, 'toPortalCode']);
            Route::post('get/province/account/type/wise/merchant',     [ParcelController::class, 'getProvinceAndAccountTypeWiseMerchant'])->name('get.province.account_type.wise.merchant');
            Route::post('get/merchant/customer',     [ParcelController::class, 'getMerchantCustomer'])->name('get.merchant.customer');
            Route::post('parcel/customer/info',     [ParcelController::class, 'parcelCustomerInfo'])->name('parcel.customer.info');
            Route::post('customer/document/submit',     [MerchantController::class, 'merchantDocumentSubmit'])->name('customer.document.submit');

            // Admin Routes
            Route::group(['prefix' => 'admin'], function () {
                Route::get('province/wise/city/{id}', [MerchantController::class, 'getProvinceCity'])
                    ->name('admin.province.wise.city');

                Route::get('roles',                                                     [RoleController::class, 'index'])->name('roles.index')->middleware('hasPermission:role_read');
                Route::get('roles/create',                                              [RoleController::class, 'create'])->name('roles.create')->middleware('hasPermission:role_create');
                Route::post('roles/store',                                              [RoleController::class, 'store'])->name('roles.store')->middleware('hasPermission:role_create');
                Route::get('roles/edit/{id}',                                           [RoleController::class, 'edit'])->name('roles.edit')->middleware('hasPermission:role_update');
                Route::get('roles/clone/{id}',                                          [RoleController::class, 'clone'])->name('roles.clone')->middleware('hasPermission:role_create');
                Route::post('roles/store/clone',                                        [RoleController::class, 'storeDuplicate'])->name('roles.store.clone')->middleware('hasPermission:role_create');
                Route::put('roles/update',                                              [RoleController::class, 'update'])->name('roles.update')->middleware('hasPermission:role_update');
                Route::delete('role/delete/{id}',                                       [RoleController::class, 'destroy'])->name('role.delete')->middleware('hasPermission:role_delete');
                Route::get('users',          [UserController::class, 'index'])->name('users.index')->middleware('hasPermission:user_read');
                Route::get('users/filter',   [UserController::class, 'filter'])->name('users.filter')->middleware('hasPermission:user_read');
                Route::get('users/create',   [UserController::class, 'create'])->name('users.create')->middleware('hasPermission:user_create');
                Route::post('users/store',   [UserController::class, 'store'])->name('users.store')->middleware('hasPermission:user_create');
                Route::get('users/edit/{id}', [UserController::class, 'edit'])->name('users.edit')->middleware('hasPermission:user_update');
                Route::put('users/update',   [UserController::class, 'update'])->name('users.update')->middleware('hasPermission:user_update');
                Route::get('users/permissions/{id}',  [UserController::class, 'permission'])->name('users.permission')->middleware('hasPermission:permission_update');
                Route::put('users/permissions/update', [UserController::class, 'permissionsUpdate'])->name('users.permissions.update')->middleware('hasPermission:permission_update');
                Route::delete('user/delete/{id}',     [UserController::class, 'destroy'])->name('user.delete')->middleware('hasPermission:user_delete');
                // Account income
                Route::get('income',                      [IncomeController::class, 'index'])->name('income.index')->middleware('hasPermission:income_read');
                Route::get('income/filter',               [IncomeController::class, 'filter'])->name('income.filter')->middleware('hasPermission:income_read');
                Route::get('income/create',               [IncomeController::class, 'create'])->name('income.create')->middleware('hasPermission:income_create');
                Route::post('income/search-account/{id}', [IncomeController::class, 'searchAccount'])->name('income.search-account');
                Route::post('income/store',               [IncomeController::class, 'store'])->name('income.store')->middleware('hasPermission:income_create');
                Route::get('income/edit/{id}',            [IncomeController::class, 'edit'])->name('income.edit')->middleware('hasPermission:income_update');
                Route::put('income/update/{id}',          [IncomeController::class, 'update'])->name('income.update')->middleware('hasPermission:income_update');
                Route::delete('income/delete/{id}',       [IncomeController::class, 'destroy'])->name('income.delete')->middleware('hasPermission:income_delete');
                Route::post('income/balance-check',       [IncomeController::class, 'balanceCheck'])->name('income.balance.check');
                Route::post('income/users',               [IncomeController::class, 'IncomeUsers'])->name('income.users');
                // Account expense
                Route::get('expense',                      [ExpenseController::class, 'index'])->name('expense.index')->middleware('hasPermission:expense_read');
                Route::get('expense/filter',               [ExpenseController::class, 'filter'])->name('expense.filter')->middleware('hasPermission:expense_read');
                Route::get('expense/create',               [ExpenseController::class, 'create'])->name('expense.create')->middleware('hasPermission:expense_create');
                Route::post('expense/search-account/{id}', [ExpenseController::class, 'searchAccount'])->name('expense.search-account');
                Route::post('expense/store',               [ExpenseController::class, 'store'])->name('expense.store')->middleware('hasPermission:expense_create');
                Route::get('expense/edit/{id}',            [ExpenseController::class, 'edit'])->name('expense.edit')->middleware('hasPermission:expense_update');
                Route::put('expense/update/{id}',          [ExpenseController::class, 'update'])->name('expense.update')->middleware('hasPermission:expense_update');
                Route::delete('expense/delete/{id}',       [ExpenseController::class, 'destroy'])->name('expense.delete')->middleware('hasPermission:expense_delete');
                Route::post('expense/users',               [ExpenseController::class, 'ExpenseUsers'])->name('expense.users');
                //salary
                Route::get('salarys',                      [SalaryController::class, 'index'])->name('salary.index')->middleware('hasPermission:salary_read');
                Route::get('salarys/filter',                [SalaryController::class, 'salaryFilter'])->name('salary.filter')->middleware('hasPermission:salary_read');
                Route::get('salarys/create',                [SalaryController::class, 'create'])->name('salary.create')->middleware('hasPermission:salary_create');
                Route::post('salary/users',               [SalaryController::class, 'Users'])->name('salary.users');
                Route::post('salary/store',               [SalaryController::class, 'store'])->name('salary.store')->middleware('hasPermission:salary_create');
                Route::get('salarys/edit/{id}',             [SalaryController::class, 'edit'])->name('salary.edit')->middleware('hasPermission:salary_update');
                Route::put('salary/update',              [SalaryController::class, 'update'])->name('salary.update')->middleware('hasPermission:salary_update');
                Route::delete('salary/delete/{id}',        [SalaryController::class, 'delete'])->name('salary.delete')->middleware('hasPermission:salary_delete');
                Route::post('salary/search-account',       [SalaryController::class, 'salaryGet'])->name('salary.account.search');
                Route::get('salary/pay-slip/{id}',         [SalaryController::class, 'paySlip'])->name('salary.pay.slip')->middleware('hasPermission:salary_read');
                Route::get('bank-transaction',                 [BankTransactionController::class, 'index'])->name('bank-transaction.index')->middleware('hasPermission:bank_transaction_read');
                Route::Post('bank-transaction/filter',         [BankTransactionController::class, 'filter'])->name('bank-transaction.filter')->middleware('hasPermission:bank_transaction_read');
                Route::get('bank-transaction/specific/search', [BankTransactionController::class, 'bankTransactionSpecificSearch'])->name('bank.transaction.specific.search');
                Route::get('bank-transaction/filter/print',    [BankTransactionController::class, 'bankTransactionPrint'])->name('bank.transaction.filter.print');
                // User profile
                Route::get('profile',                  [ProfileController::class, 'view'])->name('profile.index');
                Route::get('profile/update',           [ProfileController::class, 'create'])->name('profile.edit');
                Route::put('profile/update',           [ProfileController::class, 'update'])->name('profile.update');
                Route::get('profile/change-password',  [ProfileController::class, 'changePassword'])->name('password.change');
                Route::put('profile/update-password',  [ProfileController::class, 'updatePassword'])->name('profile.password.update');
                // Disputes
                Route::get('disputes', [AdminParcelDisputeController::class, 'index'])->name('admin.disputes.index')->middleware('hasPermission:dispute_read');
                Route::get('disputes/{id}', [AdminParcelDisputeController::class, 'show'])->name('admin.disputes.show')->middleware('hasPermission:dispute_read');
                Route::put('disputes/{id}', [AdminParcelDisputeController::class, 'update'])->name('admin.disputes.update')->middleware('hasPermission:dispute_update');
                Route::get('platform-ledger', [PlatformLedgerController::class, 'index'])->name('admin.platform-ledger.index')->middleware('hasPermission:platform_ledger_read');
                // Merchant Routes

                Route::get('customer/index',          [MerchantController::class, 'index'])->name('customer.index')->middleware('hasPermission:customer_read');
                Route::get('customer/create',         [MerchantController::class, 'create'])->name('customer.create')->middleware('hasPermission:customer_create');
                Route::post('customer/store',         [MerchantController::class, 'store'])->name('customer.store')->middleware('hasPermission:customer_create');
                Route::get('customer/edit/{id}',      [MerchantController::class, 'edit'])->name('customer.edit')->middleware('hasPermission:customer_update');
                Route::put('customer/update/{id}',    [MerchantController::class, 'update'])->name('customer.update')->middleware('hasPermission:customer_update');
                Route::delete('customer/delete/{id}', [MerchantController::class, 'destroy'])->name('customer.delete')->middleware('hasPermission:customer_delete');
                Route::get('customer/view/{id}',      [MerchantController::class, 'view'])->name('customer.view')->middleware('hasPermission:customer_view');
                Route::get('customer/account_status/{id}',    [MerchantController::class, 'accountStatus'])->name('customer.account_status')->middleware('hasPermission:customer_update');
                Route::get('customer/verification_status/{id}',    [MerchantController::class, 'verificationStatus'])->name('customer.verification_status')->middleware('hasPermission:customer_update');
                Route::get('customer/document_status/{id}',    [MerchantController::class, 'documentStatus'])->name('customer.document_status')->middleware('hasPermission:customer_update');

                //Merchent delivery charge routes
                Route::post('sender/delivery-charge/info',                    [MerchantDeliveryChargeController::class, 'deliveryChargeInfo'])->name('merchant.deliveryCharge.deliveryChargeInfo');
                Route::get('sender/{merchant}/delivery-charge/index',         [MerchantDeliveryChargeController::class, 'index'])->name('merchant.deliveryCharge.index')->middleware('hasPermission:merchant_delivery_charge_read');
                Route::get('sender/{merchant}/delivery-charge/create',        [MerchantDeliveryChargeController::class, 'create'])->name('merchant.deliveryCharge.create')->middleware('hasPermission:merchant_delivery_charge_create');
                Route::post('sender/{merchant}/delivery-charge/store',        [MerchantDeliveryChargeController::class, 'store'])->name('merchant.deliveryCharge.store')->middleware('hasPermission:merchant_delivery_charge_create');
                Route::get('sender/{merchant}/delivery-charge/edit/{id}',     [MerchantDeliveryChargeController::class, 'edit'])->name('merchant.deliveryCharge.edit')->middleware('hasPermission:merchant_delivery_charge_update');
                Route::put('sender/{merchant}/delivery-charge/update/{id}',   [MerchantDeliveryChargeController::class, 'update'])->name('merchant.deliveryCharge.update')->middleware('hasPermission:merchant_delivery_charge_update');
                Route::delete('sender/{merchant}/delivery-charge/delete/{id}', [MerchantDeliveryChargeController::class, 'delete'])->name('merchant.deliveryCharge.delete')->middleware('hasPermission:merchant_delivery_charge_delete');
                //Merchent shops routes
                Route::get('sender/{id}/shops/index',     [MerchantShopsController::class, 'index'])->name('merchant.shops.index')->middleware('hasPermission:merchant_shop_read');
                Route::get('sender/shops/create/{id}',    [MerchantShopsController::class, 'create'])->name('merchant.shops.create')->middleware('hasPermission:merchant_shop_create');
                Route::post('sender/shops/store',         [MerchantShopsController::class, 'store'])->name('merchant.shops.store')->middleware('hasPermission:merchant_shop_create');
                Route::get('sender/shops/edit/{id}',      [MerchantShopsController::class, 'edit'])->name('merchant.shops.edit')->middleware('hasPermission:merchant_shop_update');
                Route::put('sender/shops/update',         [MerchantShopsController::class, 'update'])->name('merchant.shops.update')->middleware('hasPermission:merchant_shop_update');
                Route::delete('sender/shops/delete/{id}', [MerchantShopsController::class, 'delete'])->name('merchant.shops.delete')->middleware('hasPermission:merchant_shop_delete');
                Route::get('sender/shops/default/{merchant_id}/{id}', [MerchantShopsController::class, 'defaultShop'])->name('merchant.shops.default');

                Route::get('sender/customers/index/{id}',     [SenderCustomerController::class, 'index'])->name('sender_customers.index');
                Route::get('sender/customers/create/{id}',    [SenderCustomerController::class, 'create'])->name('sender_customers.create');
                Route::post('sender/customers/store',         [SenderCustomerController::class, 'store'])->name('sender_customers.store');
                Route::get('sender/customers/edit/{id}',      [SenderCustomerController::class, 'edit'])->name('sender_customers.edit');
                Route::put('sender/customers/update/{id}',    [SenderCustomerController::class, 'update'])->name('sender_customers.update');
                Route::delete('sender/customers/delete/{id}', [SenderCustomerController::class, 'destroy'])->name('sender_customers.delete');
                Route::get('sender/customers/view/{id}',      [SenderCustomerController::class, 'view'])->name('sender_customers.view');

                //merchant payment account
                Route::get('sender/{id}/payment/index',       [MerchantPaymentAccountController::class, 'index'])->name('merchant.paymentaccount.index')->middleware('hasPermission:merchant_payment_read');
                Route::get('sender/{id}/payment/add',         [MerchantPaymentAccountController::class, 'paymentAdd'])->name('merchant.payment.add')->middleware('hasPermission:merchant_payment_create');
                Route::post('sender/paymentmethod/change',    [MerchantPaymentAccountController::class, 'paymentChange'])->name('merchant.paymentmethod.change');
                Route::post('sender/paymentinfo/bank/store',  [MerchantPaymentAccountController::class, 'bankStore'])->name('merchant.paymentinfo.bank.store')->middleware('hasPermission:merchant_payment_create');
                Route::post('sender/paymentinfo/mobile/store', [MerchantPaymentAccountController::class, 'mobileStore'])->name('merchant.paymentinfo.mobile.store')->middleware('hasPermission:merchant_payment_create');
                Route::get('sender/{mid}/payment/edit/{id}',  [MerchantPaymentAccountController::class, 'paymentEdit'])->name('merchant.payment.edit')->middleware('hasPermission:merchant_payment_update');
                Route::put('sender/paymentinfo/bank/update',   [MerchantPaymentAccountController::class, 'bankUpdate'])->name('merchant.payment.bank.update')->middleware('hasPermission:merchant_payment_update');
                Route::put('sender/paymentinfo/mobile/update', [MerchantPaymentAccountController::class, 'mobileUpdate'])->name('merchant.payment.mobile.update')->middleware('hasPermission:merchant_payment_update');
                Route::delete('sender/paymentinfo/delete/{id}', [MerchantPaymentAccountController::class, 'destroy'])->name('merchant.payment.delete')->middleware('hasPermission:merchant_payment_delete');
                //merchant manage payment
                Route::get('payment/index',         [MerchantmanagePaymentController::class, 'index'])->name('merchant.manage.payment.index')->middleware('hasPermission:payment_read');
                Route::get('payment/create',        [MerchantmanagePaymentController::class, 'create'])->name('merchant-manage.payment.create')->middleware('hasPermission:payment_create');
                Route::post('sender/account',     [MerchantmanagePaymentController::class, 'merchantAccount'])->name('merchant-manage.merchant.account');
                Route::post('sender/search',      [MerchantmanagePaymentController::class, 'merchantSearch'])->name('merchant-manage.merchant-search');
                Route::post('payment/store',        [MerchantmanagePaymentController::class, 'paymentStore'])->name('merchantmanage.payment.store')->middleware('hasPermission:payment_create');
                Route::get('payment/edit/{id}',     [MerchantmanagePaymentController::class, 'edit'])->name('merchatmanage.payment.edit')->middleware('hasPermission:payment_update');
                Route::put('payment/update',        [MerchantmanagePaymentController::class, 'update'])->name('merchantmanage.payment.update')->middleware('hasPermission:payment_update');
                Route::delete('payment/delete/{id}', [MerchantmanagePaymentController::class, 'destroy'])->name('merchantmanage.payment.delete')->middleware('hasPermission:payment_delete');
                //merchant manage payment process
                Route::get('payment/reject/{id}',        [MerchantmanagePaymentController::class, 'reject'])->name('merchantmanage.payment.reject')->middleware('hasPermission:payment_reject');
                Route::get('payment/cancel-reject/{id}', [MerchantmanagePaymentController::class, 'cancelReject'])->name('merchantmanage.payment.cancel-reject')->middleware('hasPermission:payment_reject');
                Route::get('payment/process/{id}',       [MerchantmanagePaymentController::class, 'process'])->name('merchantmanage.payment.process')->middleware('hasPermission:payment_process');
                Route::get('payment/cancel-process/{id}', [MerchantmanagePaymentController::class, 'cancelProcess'])->name('merchantmanage.payment.cancel-process')->middleware('hasPermission:payment_process');
                Route::put('payment/processed',          [MerchantmanagePaymentController::class, 'processed'])->name('merchantmanage.payment.processed')->middleware('hasPermission:payment_process');
                Route::get('payment/sender/filter',    [MerchantmanagePaymentController::class, 'merchantpaymentFilter'])->name('merchantmanage.payment.filter');
                //merchant invoice
                Route::prefix('sender/{merchant_id}/invoice')->name('merchant.invoice.')->group(function () {
                    Route::get('/',                      [MerchantInvoiceController::class, 'index'])->name('index')->middleware('hasPermission:invoice_read');
                    Route::get('/{invoice_id}',          [MerchantInvoiceController::class, 'InvoiceDetails'])->name('details')->middleware('hasPermission:invoice_read');
                    Route::get('/status/update',         [MerchantInvoiceController::class, 'StatusUpdate'])->name('status.update')->middleware('hasPermission:invoice_status_update');
                    Route::get('/pdf/{invoice_id}',     [MerchantInvoiceController::class, 'InvoicePdf'])->name('pdf')->middleware('hasPermission:invoice_read');
                    Route::get('/csv/{invoice_id}',     [MerchantInvoiceController::class, 'InvoiceCSV'])->name('csv')->middleware('hasPermission:invoice_read');
                });
                Route::get('paid/invoice',               [MerchantInvoiceController::class, 'PaidInvoice'])->name('paid.invoice.index');

                //liquid fragile
                Route::get('liquid-fragile/index',  [LiquidFragileController::class, 'index'])->name('liquid-fragile.index')->middleware('hasPermission:liquid_fragile_read');
                Route::get('liquid-fragile/edit',   [LiquidFragileController::class, 'edit'])->name('liquid.fragile.edit')->middleware('hasPermission:liquid_fragile_update');
                Route::put('liquid-fragile/update', [LiquidFragileController::class, 'update'])->name('liquid.fragile.update')->middleware('hasPermission:liquid_fragile_update');
                Route::post('liquid-fragile/status', [LiquidFragileController::class, 'status'])->name('liquid-fragile.status')->middleware('hasPermission:liquid_status_change');

                Route::resource('extra_cost', ExtraCostController::class);
                Route::controller(ExtraCostController::class)->group(function () {
                    Route::post('extra_cost/status', 'status')->name('extra_cost.status');
                });

                // Parcel Routes
                Route::get('order/index',                          [ParcelController::class, 'index'])->name('parcel.index')->middleware('hasPermission:parcel_read');
                Route::get('order/details/{id}',                   [ParcelController::class, 'details'])->name('parcel.details')->middleware('hasPermission:parcel_read');
                Route::get('order/logs/{id}',                      [ParcelController::class, 'logs'])->name('parcel.logs')->middleware('hasPermission:parcel_read');
                Route::get('order/clone/{id}',                     [ParcelController::class, 'duplicate'])->name('parcel.clone');
                Route::get('order/create',                         [ParcelController::class, 'create'])->name('parcel.create')->middleware('hasPermission:parcel_create');
                Route::post('order/store',                         [ParcelController::class, 'store'])->name('parcel.store')->middleware('hasPermission:parcel_create');
                Route::post('order/clone-store',                   [ParcelController::class, 'duplicateStore'])->name('parcel.clone-store');
                Route::get('order/edit/{id}',                      [ParcelController::class, 'edit'])->name('parcel.edit')->middleware('hasPermission:parcel_update');
                Route::put('order/update/{id}',                    [ParcelController::class, 'update'])->name('parcel.update')->middleware('hasPermission:parcel_update');
                Route::get('order/status-update/{id}/{status_id}', [ParcelController::class, 'statusUpdate'])->name('parcel.status-update')->middleware('hasPermission:parcel_status_update');
                Route::delete('order/delete/{id}',                 [ParcelController::class, 'destroy'])->name('parcel.delete')->middleware('hasPermission:parcel_delete');
                Route::get('order/print/{id}',                     [ParcelController::class, 'parcelPrint'])->name('parcel.print')->middleware('hasPermission:parcel_read');
                Route::get('return/order/print/{id}',              [ParcelController::class, 'returnParcelPrint'])->name('return.parcel.print')->middleware('hasPermission:parcel_read');
                Route::get('order/print/{id}/label',               [ParcelController::class, 'parcelPrintLabel'])->name('parcel.print-label')->middleware('hasPermission:parcel_read');
                Route::get('order/multiple/print/label',           [ParcelController::class, 'parcelMultiplePrintLabel'])->name('parcel.multiple.print-label');
                Route::get('return/report',                        [ParcelController::class, 'parcelReturnReport'])->name('parcel.return_report')->middleware('hasPermission:parcel_read');

                //parcel add item
                Route::get('parcel/add-item',          [ParcelController::class, 'addItem'])->name('parcel.add.item');

                //parcel status
                Route::post('parcel/deliveryman/search',            [ParcelController::class, 'deliverymanSearch'])->name('parcel.deliveryman.search');
                Route::post('parcel/pickup-man/assigned',           [ParcelController::class, 'PickupManAssigned'])->name('parcel.pickup.man-assigned')->middleware('hasPermission:parcel_status_update');
                Route::post('parcel/pickup-man/assigned/cancel',    [ParcelController::class, 'PickupManAssignedCancel'])->name('parcel.pickup.man-assigned-cancel')->middleware('hasPermission:parcel_status_update');

                Route::post('parcel/ready/to/reassign',             [ParcelController::class, 'readyToReassign'])->name('parcel.ready_to_reassign')->middleware('hasPermission:parcel_status_update');
                Route::post('parcel/ready/to/reassign/booking',     [ParcelController::class, 'readyToReassignBooking'])->name('parcel.ready_to_reassign_booking')->middleware('hasPermission:parcel_status_update');
                Route::post('parcel/confirmed/booking',             [ParcelController::class, 'confirmedBooking'])->name('parcel.confirmed_booking')->middleware('hasPermission:parcel_status_update');
                Route::post('parcel/order/processing',             [ParcelController::class, 'orderProcessing'])->name('parcel.order_processing')->middleware('hasPermission:parcel_status_update');

                Route::post('parcel/pickup/re-schedule',            [ParcelController::class, 'PickupReSchedule'])->name('parcel.pickup.re.schedule')->middleware('hasPermission:parcel_status_update');
                Route::post('parcel/pickup-reschedule/cancel',      [ParcelController::class, 'PickupReScheduleCancel'])->name('parcel.pickup.re-schedule-cancel')->middleware('hasPermission:parcel_status_update');
                Route::post('parcel/pickup/received',               [ParcelController::class, 'receivedBypickupman'])->name('parcel.received.by.pickup')->middleware('hasPermission:parcel_status_update');
                Route::post('parcel/pickup/received/cancel',        [ParcelController::class, 'receivedBypickupmanCancel'])->name('parcel.pickup.man-received-cancel')->middleware('hasPermission:parcel_status_update');
                Route::post('parcel/received-warehouse',            [ParcelController::class, 'receivedWarehouse'])->name('parcel.received.warehouse')->middleware('hasPermission:parcel_status_update');
                Route::post('parcel/received-warehouse/cancel',     [ParcelController::class, 'receivedWarehouseCancel'])->name('parcel.received-warehouse-cancel')->middleware('hasPermission:parcel_status_update');
                Route::get('order/filter',                          [ParcelController::class, 'filter'])->name('parcel.filter');
                Route::post('parcel/search',                        [ParcelController::class, 'search'])->name('parcel.search');
                Route::post('parcel/search-delivery-man-assing-multiple-parcel', [ParcelController::class, 'searchDeliveryManAssingMultipleParcel'])->name('parcel.search-delivery-man-assing-multiple-parcel');
                Route::post('parcel/search-expense',                [ParcelController::class, 'searchExpense'])->name('parcel.search-expense');
                Route::post('parcel/search-income',                 [ParcelController::class, 'searchIncome'])->name('parcel.search-income');
                Route::post('parcel/delivery-man-assign-multiple-parcel', [ParcelController::class, 'deliveryManAssignMultipleParcel'])->name('parcel.delivery-man-assign-multiple-parcel')->middleware('hasPermission:parcel_status_update');
                Route::post('parcel/delivery-man-assign',            [ParcelController::class, 'deliverymanAssign'])->name('parcel.delivery-man-assign')->middleware('hasPermission:parcel_status_update');
                Route::post('parcel/delivery-man/assign/cancel',     [ParcelController::class, 'deliverymanAssignCancel'])->name('parcel.delivery-man-assign-cancel')->middleware('hasPermission:parcel_status_update');
                Route::get('order/bulkassign/print',                 [ParcelController::class, 'ParcelBulkAssignPrint'])->name('parcel.parcel-bulkassign-print');
                Route::post('parcel/delivery-reschedule',            [ParcelController::class, 'deliveryReschedule'])->name('parcel.delivery.reschedule')->middleware('hasPermission:parcel_status_update');
                Route::post('parcel/delivery-re-scheule/cancel',     [ParcelController::class, 'deliveryReScheduleCancel'])->name('parcel.delivery-re-schedule-cancel')->middleware('hasPermission:parcel_status_update');
                Route::post('parcel/return-to-qourier',              [ParcelController::class, 'returntoQourier'])->name('parcel.return-to-qourier')->middleware('hasPermission:parcel_status_update');
                Route::post('parcel/return-to-qourier-cancel',       [ParcelController::class, 'returntoQourierCancel'])->name('parcel.return-to-courier-cancel')->middleware('hasPermission:parcel_status_update');
                Route::post('parcel/return-assign-to-merchant',      [ParcelController::class, 'returnAssignToMerchant'])->name('parcel.return-assign-to-merchant')->middleware('hasPermission:parcel_status_update');
                Route::post('parcel/return-assign-to-merchant/cancel', [ParcelController::class, 'returnAssignToMerchantCancel'])->name('parcel.return-assign-to-merchant-cancel')->middleware('hasPermission:parcel_status_update');
                Route::post('parcel/return-assign-to-merchant-reschedule', [ParcelController::class, 'returnAssignToMerchantReschedule'])->name('parcel.return-assign-to-merchant.reschedule')->middleware('hasPermission:parcel_status_update');
                Route::post('parcel/return-assign-re-schedule-to-merchant/cancel', [ParcelController::class, 'returnAssignToMerchantRescheduleCancel'])->name('parcel.return-assign-re-schedule-to-merchant-cancel')->middleware('hasPermission:parcel_status_update');
                Route::post('parcel/return-received-by-merchant',       [ParcelController::class, 'returnReceivedByMerchant'])->name('parcel.return-received-by-merchant')->middleware('hasPermission:parcel_status_update');
                Route::post('parcel/return-received-by-merchant/cancel', [ParcelController::class, 'returnReceivedByMerchantCancel'])->name('parcel.return-received-by-merchant-cancel')->middleware('hasPermission:parcel_status_update');
                Route::post('parcel/delivered',                         [ParcelController::class, 'parcelDelivered'])->name('parcel.delivered')->middleware('hasPermission:parcel_status_update');
                Route::post('parcel/delivered/cancel',                  [ParcelController::class, 'parcelDeliveredCancel'])->name('parcel.delivered-cancel')->middleware('hasPermission:parcel_status_update');
                Route::post('parcel/partial-delivered',                 [ParcelController::class, 'parcelPartialDelivered'])->name('parcel.partial-delivered')->middleware('hasPermission:parcel_status_update');
                Route::post('parcel/partial-delivered/cancel',          [ParcelController::class, 'parcelPartialDeliveredCancel'])->name('parcel.partial-delivered-cancel')->middleware('hasPermission:parcel_status_update');
                Route::post('assign-pickup/parcel/search',              [ParcelController::class, 'AssignPickupParcelSearch'])->name('assign-pickup.parcel.search'); //ajax
                Route::post('assign-pickup/bulk',                       [ParcelController::class, 'AssignPickupBulk'])->name('parcel.assign-pickup-bulk')->middleware('hasPermission:parcel_status_update');
                Route::post('assign-return-to-merchant/parcel/search',  [ParcelController::class, 'AssignReturnToMerchantParcelSearch'])->name('assign-return-to-merchant.parcel.search'); //ajax
                Route::post('parcel/assign-return-to-merchant-bulk',    [ParcelController::class, 'AssignReturnToMerchantBulk'])->name('parcel.assign-return-to-merchant-bulk')->middleware('hasPermission:parcel_status_update');
                Route::post('parcel/parcel-cancel',                     [ParcelController::class, 'ParcelCancel'])->name('parcel.parcel_cancel')->middleware('hasPermission:parcel_status_update');
                Route::post('parcel/dynamic/status-update',             [ParcelController::class, 'ParcelStatusUpdate'])->name('parcel.dynamic.status.update')->middleware('hasPermission:parcel_status_update');

                // new route add
                Route::post('parcel/priority/update',                   [ParcelController::class, 'priorityUpdate'])->name('parcel.priority.status');
                Route::get('parcel/deliveryMan/show',                   [ParcelController::class, 'parcelDeliveryMan'])->name('parcel.parcelDeliveryMan');
                Route::get('parcel/delivered/logs/info/{id}',           [ParcelController::class, 'deliveredInfo'])->name('parcel.deliveredInfo');
                Route::get('order/placed/{id}',                         [ParcelController::class,  'Placed'])->name('parcel.placed');
                //end parcel status
                Route::post('parcel/merchant',                          [ParcelController::class, 'getMerchant'])->name('parcel.merchant.get');
                Route::post('parcel/merchant/shops',                    [ParcelController::class, 'merchantShops'])->name('parcel.merchant.shops');
                Route::post('parcel/delivery-category',                 [ParcelController::class, 'deliveryWeight'])->name('parcel.deliveryCategory.deliveryWeight');
                Route::post('parcel/delivery-charge',                   [ParcelController::class, 'deliveryCharge'])->name('parcel.deliveryCharge.get');
                Route::post('parcel/delivery-charge/unit/parcel',       [ParcelController::class, 'deliveryChargeUnitParcel'])->name('parcel.deliveryCharge.unitParcel.get');
                Route::post('parcel/mpesa/pay',                         [MerchantPanelMpesaController::class, 'pay'])->name('parcel.mpesa.pay');
                //import
                Route::get('order/import-parcel',                      [ParcelController::class, 'parcelImportExport'])->name('parcel.parcel-import')->middleware('hasPermission:parcel_create');
                Route::post('parcel/file-import',                       [ParcelController::class, 'parcelImport'])->name('parcel.file-import')->middleware('hasPermission:parcel_create');
                Route::get('order/file-export',                        [ParcelController::class, 'parcelExport'])->name('parcel.file-export');
                Route::post('parcel/import/merchant',                   [ParcelController::class, 'getImportMerchant'])->name('parcel.import.merchant.get');
                //merchant fetch using ajax
                Route::post('get-merchant-cod',                         [parcelController::class, 'getMerchantCod'])->name('get.merchant.cod');

                // Deliveryman
                Route::get('driver',                [DeliveryManController::class, 'index'])->name('deliveryman.index')->middleware('hasPermission:delivery_man_read');
                Route::get('driver/filter',         [DeliveryManController::class, 'filter'])->name('deliveryman.filter')->middleware('hasPermission:delivery_man_read');
                Route::get('driver/create',         [DeliveryManController::class, 'create'])->name('deliveryman.create')->middleware('hasPermission:delivery_man_create');
                Route::post('driver/store',         [DeliveryManController::class, 'store'])->name('deliveryman.store')->middleware('hasPermission:delivery_man_create');
                Route::get('driver/edit/{id}',      [DeliveryManController::class, 'edit'])->name('deliveryman.edit')->middleware('hasPermission:delivery_man_update');
                Route::put('driver/update',         [DeliveryManController::class, 'update'])->name('deliveryman.update')->middleware('hasPermission:delivery_man_update');
                Route::delete('driver/delete/{id}', [DeliveryManController::class, 'destroy'])->name('deliveryman.delete')->middleware('hasPermission:delivery_man_delete');
                Route::get('driver/details/{id}', [DeliveryManController::class, 'details'])->name('deliveryman.details')->middleware('hasPermission:delivery_man_read');
                Route::get('driver/account_status/{id}', [DeliveryManController::class, 'accountStatus'])->name('deliveryman.account_status')->middleware('hasPermission:delivery_man_update');
                Route::get('driver/verification_status/{id}', [DeliveryManController::class, 'verificationStatus'])->name('deliveryman.verification_status')->middleware('hasPermission:delivery_man_update');
                Route::get('driver/document_status/{id}', [DeliveryManController::class, 'documentStatus'])->name('deliveryman.document_status')->middleware('hasPermission:delivery_man_update');
                Route::get('driver/kyc-review', [DeliveryManController::class, 'kycIndex'])->name('deliveryman.kyc.index')->middleware('hasPermission:delivery_man_read');
                Route::post('driver/kyc/approve/{id}', [DeliveryManController::class, 'approveKyc'])->name('deliveryman.kyc.approve')->middleware('hasPermission:delivery_man_update');
                Route::post('driver/kyc/reject/{id}', [DeliveryManController::class, 'rejectKyc'])->name('deliveryman.kyc.reject')->middleware('hasPermission:delivery_man_update');
                Route::post('driver/kyc/reupload/{id}', [DeliveryManController::class, 'requestReupload'])->name('deliveryman.kyc.reupload')->middleware('hasPermission:delivery_man_update');

                // Delivery type Routes
                Route::get('delivery-type/index',          [DeliveryTypeController::class, 'index'])->name('delivery-type.index')->middleware('hasPermission:delivery_type_read');
                // Route::get('delivery-type/create',      [DeliveryTypeController::class, 'create'])->name('delivery-type.create')->middleware('hasPermission:delivery_type_create');
                Route::post('delivery-type/store',         [DeliveryTypeController::class, 'store'])->name('delivery-type.store')->middleware('hasPermission:delivery_type_create');
                Route::get('delivery-type/edit/{id}',      [DeliveryTypeController::class, 'edit'])->name('delivery-type.edit')->middleware('hasPermission:delivery_type_update');
                Route::put('delivery-type/update',         [DeliveryTypeController::class, 'update'])->name('delivery-type.update')->middleware('hasPermission:delivery_type_update');
                Route::delete('delivery-type/delete/{id}', [DeliveryTypeController::class, 'destroy'])->name('delivery-type.delete')->middleware('hasPermission:delivery_type_delete');

                // Shipping type Routes
                Route::get('shipping-type/index',          [ShippingTypeController::class, 'index'])->name('shipping-type.index')->middleware('hasPermission:shipping_type_read');
                // Route::get('shipping-type/create',         [ShippingTypeController::class, 'create'])->name('shipping-type.create')->middleware('hasPermission:shipping_type_create');
                // Route::post('shipping-type/store',         [ShippingTypeController::class, 'store'])->name('shipping-type.store')->middleware('hasPermission:shipping_type_create');
                Route::get('shipping-type/edit/{id}',      [ShippingTypeController::class, 'edit'])->name('shipping-type.edit')->middleware('hasPermission:shipping_type_update');
                Route::put('shipping-type/update',         [ShippingTypeController::class, 'update'])->name('shipping-type.update')->middleware('hasPermission:shipping_type_update');
                Route::delete('shipping-type/delete/{id}', [ShippingTypeController::class, 'destroy'])->name('shipping-type.delete')->middleware('hasPermission:shipping_type_delete');
                Route::get('get-shipping-type',            [ShippingTypeController::class,  'getShippingType'])->name('get.shipping.type');
                Route::get('get-shipping-types',            [ShippingTypeController::class,  'getShippingTypes'])->name('get.shipping.types');

                // Delivery Charges Routes
                Route::get('delivery-charge/index',         [DeliveryChargeController::class, 'index'])->name('delivery-charge.index')->middleware('hasPermission:delivery_charge_read');
                Route::get('delivery-charge/filter',         [DeliveryChargeController::class, 'filter'])->name('delivery-charge.filter')->middleware('hasPermission:delivery_charge_read');
                Route::get('delivery-charge/create',        [DeliveryChargeController::class, 'create'])->name('delivery-charge.create')->middleware('hasPermission:delivery_charge_create');
                Route::post('delivery-charge/store',        [DeliveryChargeController::class, 'store'])->name('delivery-charge.store')->middleware('hasPermission:delivery_charge_create');
                Route::get('delivery-charge/edit/{id}',     [DeliveryChargeController::class, 'edit'])->name('delivery-charge.edit')->middleware('hasPermission:delivery_charge_update');
                Route::get('delivery-charge/view/{id}',     [DeliveryChargeController::class, 'view'])->name('delivery-charge.view');
                Route::put('delivery-charge/update',        [DeliveryChargeController::class, 'update'])->name('delivery-charge.update')->middleware('hasPermission:delivery_charge_update');
                Route::delete('delivery-charge/delete/{id}', [DeliveryChargeController::class, 'destroy'])->name('delivery-charge.delete')->middleware('hasPermission:delivery_charge_delete');

                Route::controller(ZoneController::class)->prefix('zone-delivery-charge')->name('settings.zone.delivery-charge.')->group(function () {
                    Route::get('/',            'index')->name('index')->middleware('hasPermission:zone_delivery_charge_read');
                    Route::get('/create',      'create')->name('create')->middleware('hasPermission:zone_delivery_charge_create');
                    Route::post('/store',      'store')->name('store')->middleware('hasPermission:zone_delivery_charge_create');
                    Route::get('/edit/{id}',   'edit')->name('edit')->middleware('hasPermission:zone_delivery_charge_update');
                    Route::put('/update',      'update')->name('update')->middleware('hasPermission:zone_delivery_charge_update');
                    Route::delete('/delete/{id}', 'delete')->name('delete')->middleware('hasPermission:zone_delivery_charge_delete');
                });

                //delivery type
                Route::get('delivery-type/index', [DeliveryTypeController::class, 'index'])->name('delivery-type.index')->middleware('hasPermission:delivery_type_read');
                Route::post('delivery-type/status', [DeliveryTypeController::class, 'status'])->name('delivery-type.status')->middleware('hasPermission:delivery_type_status_change');
                // Packaging Routes
                Route::get('packaging/index',       [PackagingController::class, 'index'])->name('packaging.index')->middleware('hasPermission:packaging_read');
                Route::get('packaging/create',      [PackagingController::class, 'create'])->name('packaging.create')->middleware('hasPermission:packaging_create');
                Route::post('packaging/store',      [PackagingController::class, 'store'])->name('packaging.store')->middleware('hasPermission:packaging_create');
                Route::get('packaging/edit/{id}',   [PackagingController::class, 'edit'])->name('packaging.edit')->middleware('hasPermission:packaging_update');
                Route::get('packaging/view/{id}',   [PackagingController::class, 'view']);
                Route::put('packaging/update',     [PackagingController::class, 'update'])->name('packaging.update')->middleware('hasPermission:packaging_update');
                Route::delete('packaging/delete/{id}', [PackagingController::class, 'destroy'])->name('packaging.delete')->middleware('hasPermission:packaging_delete');
                // Accounts Routes
                Route::get('accounts/index',          [AccountController::class, 'index'])->name('accounts.index')->middleware('hasPermission:account_read');
                Route::get('accounts/filter',         [AccountController::class, 'filter'])->name('accounts.filter')->middleware('hasPermission:account_read');
                Route::get('accounts/create',         [AccountController::class, 'create'])->name('accounts.create')->middleware('hasPermission:account_create');
                Route::post('accounts/store',         [AccountController::class, 'store'])->name('accounts.store')->middleware('hasPermission:account_create');
                Route::get('accounts/edit/{id}',      [AccountController::class, 'edit'])->name('accounts.edit')->middleware('hasPermission:account_update');
                Route::get('accounts/view/{id}',      [AccountController::class, 'view'])->name('accounts.view');
                Route::put('accounts/update/{id}',    [AccountController::class, 'update'])->name('accounts.update')->middleware('hasPermission:account_update');
                Route::delete('accounts/delete/{id}', [AccountController::class, 'destroy'])->name('accounts.delete')->middleware('hasPermission:account_delete');
                Route::post('accounts/current-balance', [AccountController::class, 'currentBalance'])->name('accounts.current-balance');
                // Fund Transfer Routes
                Route::get('fund-transfer/index',          [FundTransferController::class, 'index'])->name('fund-transfer.index')->middleware('hasPermission:fund_transfer_read');
                Route::get('fund-transfer/create',         [FundTransferController::class, 'create'])->name('fund-transfer.create')->middleware('hasPermission:fund_transfer_create');
                Route::post('fund-transfer/store',         [FundTransferController::class, 'store'])->name('fund-transfer.store')->middleware('hasPermission:fund_transfer_create');
                Route::get('fund-transfer/edit/{id}',      [FundTransferController::class, 'edit'])->name('fund-transfer.edit')->middleware('hasPermission:fund_transfer_update');
                Route::get('fund-transfer/view/{id}',      [FundTransferController::class, 'view'])->name('fund-transfer.view');
                Route::put('fund-transfer/update/{id}',    [FundTransferController::class, 'update'])->name('fund-transfer.update')->middleware('hasPermission:fund_transfer_update');
                Route::delete('fund-transfer/delete/{id}', [FundTransferController::class, 'destroy'])->name('fund-transfer.delete')->middleware('hasPermission:fund_transfer_delete');
                Route::get('fund-transfer/specific/search', [FundTransferController::class, 'fundTransferSpecificSearch'])->name('fund.transfer.specific.search')->middleware('hasPermission:fund_transfer_read');
                Route::get('fund-transfer/search/flter/print', [FundTransferController::class, 'fundTransferSearchFilterPrint'])->name('fund.transfer.search.filter.print')->middleware('hasPermission:fund_transfer_read');
                Route::get('fund-transfer/filter',         [FundTransferController::class, 'fundTransferFilter'])->name('fund.transfer.filter')->middleware('hasPermission:fund_transfer_read');
                // Designation
                Route::get('designations',              [DesignationController::class, 'index'])->name('designations.index')->middleware('hasPermission:designation_read');
                Route::get('designations/create',       [DesignationController::class, 'create'])->name('designations.create')->middleware('hasPermission:designation_create');
                Route::post('designations/store',       [DesignationController::class, 'store'])->name('designations.store')->middleware('hasPermission:designation_create');
                Route::get('designations/edit/{id}',    [DesignationController::class, 'edit'])->name('designations.edit')->middleware('hasPermission:designation_update');
                Route::put('designations/update',       [DesignationController::class, 'update'])->name('designations.update')->middleware('hasPermission:designation_update');
                Route::delete('designation/delete/{id}', [DesignationController::class, 'destroy'])->name('designation.delete')->middleware('hasPermission:designation_delete');
                // Department
                Route::get('departments',               [DepartmentController::class, 'index'])->name('departments.index')->middleware('hasPermission:department_read');
                Route::get('departments/create',        [DepartmentController::class, 'create'])->name('departments.create')->middleware('hasPermission:department_create');
                Route::post('departments/store',        [DepartmentController::class, 'store'])->name('departments.store')->middleware('hasPermission:department_create');
                Route::get('departments/edit/{id}',     [DepartmentController::class, 'edit'])->name('departments.edit')->middleware('hasPermission:department_update');
                Route::put('departments/update',        [DepartmentController::class, 'update'])->name('departments.update')->middleware('hasPermission:department_update');
                Route::delete('department/delete/{id}', [DepartmentController::class, 'destroy'])->name('department.delete')->middleware('hasPermission:department_delete');
                // Fraud
                // To_do List route
                // Support route
                Route::get('support/index',         [SupportController::class, 'index'])->name('support.index')->middleware('hasPermission:support_read');
                Route::get('support/create',        [SupportController::class, 'create'])->name('support.add')->middleware('hasPermission:support_create');
                Route::post('support/store',        [SupportController::class, 'store'])->name('support.store')->middleware('hasPermission:support_create');
                Route::get('support/edit/{id}',     [SupportController::class, 'edit'])->name('support.edit')->middleware('hasPermission:support_update');
                Route::put('support/update',        [SupportController::class, 'update'])->name('support.update')->middleware('hasPermission:support_update');
                Route::delete('support/delete/{id}', [SupportController::class, 'destroy'])->name('support.delete')->middleware('hasPermission:support_delete');
                Route::get('support/view/{id}',     [SupportController::class, 'view'])->name('support.view');
                Route::post('support/reply',        [SupportController::class, 'supportReply'])->name('support.reply')->middleware('hasPermission:support_reply');
                Route::get('support/status-update/{id}',  [SupportController::class, 'statusUpdate'])->name('support.status.update')->middleware('hasPermission:support_status_update');

                //account heads
                Route::get('/account-heads', [AccountHeadsController::class, 'index'])->name('account.heads.index')->middleware('hasPermission:account_heads_read');
                Route::get('sms-settings/index',            [SmsSettingsController::class, 'index'])->name('sms-settings.index')->middleware('hasPermission:sms_settings_read');
                Route::get('sms-settings/create',           [SmsSettingsController::class, 'create'])->name('sms-settings.create')->middleware('hasPermission:sms_settings_create');
                Route::post('sms-settings/store',           [SmsSettingsController::class, 'store'])->name('sms-settings.store')->middleware('hasPermission:sms_settings_create');
                Route::get('sms-settings/edit/{id}',        [SmsSettingsController::class, 'edit'])->name('sms-settings.edit')->middleware('hasPermission:sms_settings_update');
                Route::put('sms-settings/update/{id}',      [SmsSettingsController::class, 'update'])->name('sms-settings.update')->middleware('hasPermission:sms_settings_update');
                Route::delete('sms-settings/delete/{id}',   [SmsSettingsController::class, 'delete'])->name('sms-settings.delete')->middleware('hasPermission:sms_settings_delete');
                Route::post('sms-settings/status',          [SmsSettingsController::class, 'status'])->name('sms-settings.status')->middleware('hasPermission:sms_settings_status_change');

                Route::get('sms-send-settings/index',       [SmsSendSettingsController::class, 'index'])->name('sms-send-settings.index')->middleware('hasPermission:sms_send_settings_read');
                Route::post('sms-send-settings/status',     [SmsSendSettingsController::class, 'status'])->name('sms-send-settings.status')->middleware('hasPermission:sms_send_settings_status_change');

                // General settings
                Route::get('general-settings/index',        [GeneralSettingsController::class, 'index'])->name('general-settings.index')->middleware('hasPermission:general_settings_read');
                Route::put('general-settings/update',       [GeneralSettingsController::class, 'update'])->name('general-settings.update')->middleware('hasPermission:general_settings_update');
                Route::get('mail-settings/index',           [MailSettingsController::class, 'index'])->name('mail-settings.index')->middleware('hasPermission:general_settings_read');
                Route::put('mail-settings/update',          [MailSettingsController::class, 'update'])->name('mail-settings.update')->middleware('hasPermission:general_settings_update');

                //currency settings
                Route::get('currency',                      [CurrencyController::class, 'index'])->name('currency.index')->middleware('hasPermission:currency_read');
                Route::get('currency/create',               [CurrencyController::class, 'create'])->name('currency.create')->middleware('hasPermission:currency_create');
                Route::post('currency/store',               [CurrencyController::class, 'store'])->name('currency.store')->middleware('hasPermission:currency_create');
                Route::get('currency/edit/{id}',            [CurrencyController::class, 'edit'])->name('currency.edit')->middleware('hasPermission:currency_update');
                Route::put('currency/update',               [CurrencyController::class, 'update'])->name('currency.update')->middleware('hasPermission:currency_update');
                Route::delete('currency/delete/{id}',       [CurrencyController::class, 'delete'])->name('currency.delete')->middleware('hasPermission:currency_delete');

                // Country settings
                Route::resource('countries', CountryController::class);

                // City settings
                Route::resource('cities', CityController::class);
                Route::controller(CityController::class)->group(function () {
                    Route::get('add/multiple/city', 'addMultipleCity')->name('add_multiple_city');
                    Route::post('multiple/city/store', 'multipleCityStore')->name('multiple_city.store');
                });

                // District settings
                Route::resource('districts', DistrictController::class);
                Route::get('country/by/city/{id}', [DistrictController::class, 'countryByCity']);

                // Town settings
                Route::resource('towns', TownController::class);
                Route::get('city/by/district/{id}',       [TownController::class, 'cityByDistrict']);
                Route::get('district/by/town/{id}',       [TownController::class, 'districtByTown']);
                Route::get('town/by/portal_code/{id}',    [TownController::class, 'townByPortalCode']);





                // Parcel category


                Route::prefix('parcel-category')
                    ->controller(ParcelCategoryController::class)
                    ->name('parcel.category.')
                    ->group(function () {
                        Route::get('/',              'index')->name('index')->middleware('hasPermission:parcel_category_read');
                        Route::get('create',         'create')->name('create')->middleware('hasPermission:parcel_category_create');
                        Route::post('store',         'store')->name('store')->middleware('hasPermission:parcel_category_create');
                        Route::get('edit/{id}',      'edit')->name('edit')->middleware('hasPermission:parcel_category_update');
                        Route::put('update',         'update')->name('update')->middleware('hasPermission:parcel_category_update');
                        Route::delete('delete/{id}', 'delete')->name('delete')->middleware('hasPermission:parcel_category_delete');
                    });

                Route::prefix('province')
                    ->controller(ProvinceController::class)
                    ->name('province.')
                    ->group(function () {
                        Route::get('/',               'index')->name('index')->middleware('hasPermission:province_read');
                        Route::get('/create',         'create')->name('create')->middleware('hasPermission:province_create');
                        Route::post('/store',         'store')->name('store')->middleware('hasPermission:province_create');
                        Route::get('/edit/{id}',      'edit')->name('edit')->middleware('hasPermission:province_update');
                        Route::put('/update',         'update')->name('update')->middleware('hasPermission:province_update');
                        Route::delete('/delete/{id}', 'delete')->name('destroy')->middleware('hasPermission:province_delete');
                    });

                // News & Offer

                // Asset Routes

                // Vehicle Routes
                Route::get('vehicles/index',          [VehicleController::class, 'index'])->name('vehicles.index')->middleware('hasPermission:vehicle_read');
                Route::get('vehicles/create',         [VehicleController::class, 'create'])->name('vehicles.create')->middleware('hasPermission:vehicle_create');
                Route::post('vehicles/store',         [VehicleController::class, 'store'])->name('vehicles.store')->middleware('hasPermission:vehicle_create');
                Route::get('vehicles/edit/{id}',      [VehicleController::class, 'edit'])->name('vehicles.edit')->middleware('hasPermission:vehicle_update');
                Route::get('vehicles/view/{id}',      [VehicleController::class, 'view'])->name('vehicles.view')->middleware('hasPermission:vehicle_read');
                Route::put('vehicles/update',         [VehicleController::class, 'update'])->name('vehicles.update')->middleware('hasPermission:vehicle_update');
                Route::delete('vehicles/delete/{id}', [VehicleController::class, 'destroy'])->name('vehicles.delete')->middleware('hasPermission:vehicle_delete');

                //reports
                Route::get('reports/parcel-reports',                [ReportsController::class, 'parcelReports'])->name('parcel.reports')->middleware('hasPermission:parcel_status_reports');
                Route::get('reports/parcel-filter-reports',         [ReportsController::class, 'parcelSReports'])->name('parcel.filter.reports')->middleware('hasPermission:parcel_status_reports');
                Route::get('parcel-reports-print-page/{array}',     [ReportsController::class, 'parcelReportsPrint'])->name('parcel.reports.print.page')->middleware('hasPermission:parcel_status_reports');
                Route::get('reports/total-revenue',                  [TotalSummeryReportController::class, 'parcelTotalSummery'])->name('parcel.total.summery.index')->middleware('hasPermission:parcel_status_reports');
                Route::get('reports/total-revenue-filter',           [TotalSummeryReportController::class, 'parcelTotalSummeryFilter'])->name('parcel.filter.total.summery')->middleware('hasPermission:parcel_status_reports');
                Route::get('reports/marketplace-earnings',          [ReportsController::class, 'marketplaceEarnings'])->name('marketplace.earnings')->middleware('hasPermission:parcel_status_reports');
                Route::get('reports/rider-overview',               [ReportsController::class, 'riderOverview'])->name('rider.overview')->middleware('hasPermission:parcel_status_reports');
                Route::get('reports/rider-earnings',                 [ReportsController::class, 'riderEarnings'])->name('rider.earnings')->middleware('hasPermission:parcel_status_reports');
                Route::get('reports/completed-deliveries',           [ReportsController::class, 'completedDeliveries'])->name('reports.completed.deliveries')->middleware('hasPermission:parcel_status_reports');
                Route::get('reports/salary-report-print',           [ReportsController::class, 'SalaryReportPrint'])->name('salary.reports.print.page')->middleware('hasPermission:salary_reports');
                Route::get('rider-withdraw-requests',               [RiderWithdrawRequestController::class, 'index'])->name('rider.withdraw.requests.index');
                Route::post('rider-withdraw-requests/{id}/approve', [RiderWithdrawRequestController::class, 'approve'])->name('rider.withdraw.requests.approve');
                Route::post('rider-withdraw-requests/{id}/reject',  [RiderWithdrawRequestController::class, 'reject'])->name('rider.withdraw.requests.reject');
                Route::get('rider-wallets',                         [RiderWalletController::class, 'index'])->name('rider.wallets.index')->middleware('hasPermission:delivery_man_read');
                Route::post('rider-wallets/adjust',                 [RiderWalletController::class, 'adjust'])->name('rider.wallets.adjust')->middleware('hasPermission:delivery_man_update');
                //export
                //invoice generate
                Route::get('settings/invoice-generate-menually/index', [MerchantInvoiceController::class, 'InvoiceGenerateMenuallyIndex'])->name('invoice.generate.menually.index')->middleware('hasPermission:invoice_generate_menually');
                Route::get('settings/invoice-generate-menually',      [MerchantInvoiceController::class, 'InvoiceGenerateMenually'])->name('invoice.generate.menually')->middleware('hasPermission:invoice_generate_menually');
                //salary generate
                Route::get('salary/salary-generate',               [SalaryGenerateController::class, 'index'])->name('salary.generate.index')->middleware('hasPermission:salary_generate_read');
                Route::post('salary/salary-auto-generate',         [SalaryGenerateController::class, 'salaryAutoGenerate'])->name('salary.auto.generate')->middleware('hasPermission:salary_generate_create');
                Route::get('salary/salary-generate/create',        [SalaryGenerateController::class, 'create'])->name('salary.generate.create')->middleware('hasPermission:salary_generate_create');
                Route::post('salary/salary-generate/store',        [SalaryGenerateController::class, 'store'])->name('salary.generate.store')->middleware('hasPermission:salary_generate_create');
                Route::get('salary/salary-generate/edit/{id}',     [SalaryGenerateController::class, 'edit'])->name('salary.generate.edit')->middleware('hasPermission:salary_generate_update');
                Route::put('salary/salary-generate/update',        [SalaryGenerateController::class, 'update'])->name('salary.generate.update')->middleware('hasPermission:salary_generate_update');
                Route::delete('salary/salary-generate/delete/{id}', [SalaryGenerateController::class, 'salaryGenerateDelete'])->name('salary-generate.delete')->middleware('hasPermission:salary_generate_delete');
                Route::get('subscribe',                            [SalaryGenerateController::class, 'subscribe'])->name('subscribe.index');
                Route::get('receivers',                            [SalaryGenerateController::class, 'receivers'])->name('receivers.index');
                Route::get('receiver/export',                      [SalaryGenerateController::class, 'receiverExport'])->name('receiver.export');
                //pickup request
                Route::prefix('pickup-request')->name('pickup.request.')->group(function () {
                    Route::get('regular',                      [PickupRequestController::class, 'regular'])->name('regular')->middleware('hasPermission:pickup_request_regular');
                    Route::get('express',                      [PickupRequestController::class, 'express'])->name('express')->middleware('hasPermission:pickup_request_express');
                });
                //parcel search
                Route::get('parcel/specific/search',                    [ParcelController::class, 'ParcelSearch'])->name('parcel.specific.search');
                // Notification settings
                Route::get('notification-settings/index',        [NotificationSettingsController::class, 'index'])->name('notification-settings.index')->middleware('hasPermission:notification_settings_read');
                Route::put('notification-settings/update',       [NotificationSettingsController::class, 'update'])->name('notification-settings.update')->middleware('hasPermission:notification_settings_update');
                // push-notification
                Route::get('push-notification',                [PushNotificationController::class, 'index'])->name('push-notification.index')->middleware('hasPermission:push_notification_read');
                Route::get('push-notification/create',         [PushNotificationController::class, 'create'])->name('push-notification.create')->middleware('hasPermission:push_notification_create');
                Route::post('push-notification/store',         [PushNotificationController::class, 'store'])->name('push-notification.store')->middleware('hasPermission:push_notification_create');
                Route::delete('push-notification/delete/{id}', [PushNotificationController::class, 'destroy'])->name('push-notification.delete')->middleware('hasPermission:push_notification_delete');
                Route::post('push-notification/users',        [PushNotificationController::class, 'Users'])->name('push-notification.users');

                //Payout
                Route::prefix('payout')->name('payout.')->group(function () {
                    //stripe payment gateway
                    Route::get('/',                                     [PayoutController::class, 'index'])->name('index');
                    Route::get('/sender/payout',                      [PayoutController::class, 'merchantPayout'])->name('merchant.payout');
                    Route::get('/stripe',                               [PayoutController::class, 'stripe'])->name('merchant.stripe');
                    Route::post('/stripe/post',                         [PayoutController::class, 'stripePost'])->name('merchant.stripe.post');

                    Route::get('/razorpay',                              [PayoutController::class, 'razorpay'])->name('merchant.razorpay');
                    Route::get('/razorpay/payment',                     [PayoutController::class, 'razorpayPost'])->name('merchant.razorpay.post');

                    //paypal payment gateway
                    Route::get('paypal-index',                          [PayoutController::class, 'paypalIndex'])->name('paypal.index');
                    Route::post('paypal-payment',                       [PayoutController::class, 'paypalpayment'])->name('paypal');
                    // SSLCOMMERZ Start
                    Route::get('/sslcommerz',                 [AdminSslCommerzController::class, 'sslcommerzIndex'])->name('sslcommerz.index');
                    Route::post('/pay-via-ajax',              [AdminSslCommerzController::class, 'payViaAjax'])->name('pay.via.ajax');
                    Route::post('/success',                   [AdminSslCommerzController::class, 'success']);
                    Route::post('/fail',                      [AdminSslCommerzController::class, 'fail']);
                    Route::post('/cancel',                    [AdminSslCommerzController::class, 'cancel']);
                    Route::post('/ipn',                       [AdminSslCommerzController::class, 'ipn']);
                    //skrill payment start
                    Route::get('skrill',                      [AdminSkrillController::class, 'index'])->name('skrill.index');
                    Route::get('skrill-make-payment',         [AdminSkrillController::class, 'makePayment'])->name('skrill.make.payment');
                    Route::get('payment-completed',           [AdminSkrillController::class, 'paymentCompleted'])->name('skrill.payment.completed');
                    Route::get('payment-cancelled',           [AdminSkrillController::class, 'PaymentCancelled']);
                    //amarpay
                    Route::get('/aamarpay',                   [AdminAamarpayController::class, 'aamarpayIndex'])->name('aamarpay.index');
                    Route::get('/aamarpay-payment',           [AdminAamarpayController::class, 'payment'])->name('aamarpay.payment');
                    Route::post('/aamarpay-success',          [AdminAamarpayController::class, 'success'])->name('aamarpay.payment.success');
                    Route::post('/aamarpay-fail',             [AdminAamarpayController::class, 'fail'])->name('aamarpay.payment.fail');
                    //bkash payment
                    Route::get('/online-payment/bkash',       [AdminBkashController::class, 'index'])->name('bkash.index');
                    Route::get('bkash/redirect',              [AdminBkashController::class, 'bkashRedirect'])->name('bkash.redirect');
                    Route::get('bkash/execute',               [AdminBkashController::class, 'bkashExecute'])->name('bkash.execute');
                });
                Route::get('online-payment-list',                                [PayoutSetupController::class, 'onlinePaymentList'])->name('online.payment.list')->middleware('hasPermission:online_payment_read');
                //payout setup settings
                Route::get('/settings/pay-out/setup',                            [PayoutSetupController::class, 'index'])->name('payout.setup.settings.index')->middleware('hasPermission:payout_setup_settings_read');
                Route::put('/settings/pay-out/setup/update/{paymentmethod}',     [PayoutSetupController::class, 'PayoutSetupUpdate'])->name('payout.setup.settings.update')->middleware('hasPermission:payout_setup_settings_update');
                Route::post('/settings/pay-out/setup/mpesa-test-token',          [PayoutSetupController::class, 'testMpesaToken'])->name('mpesa.test.token')->middleware('hasPermission:payout_setup_settings_update');

                Route::get('analytics',                                           [StatisticsController::class, 'analytics'])->name('admin.analytics')->middleware('hasPermission:analytics_read');

                //Statistics
                Route::get('statistics',                                         [StatisticsController::class, 'adminStatistics'])->name('admin.statistics');
                Route::get('deliveryman-statistics',                             [StatisticsController::class, 'deliverymanStatistics'])->name('admin.deliveryman.statistics');
                //end Statistics

                Route::post('get-receiver-suggestions', [ParcelController::class, 'receiverSuggestions'])->name('get.receiver.suggestions');

                // Front Web CMS (Website Setup)
                Route::prefix('front-web')->group(function () {
                    Route::prefix('social-link')
                        ->name('social.link.')
                        ->controller(SocialLinkController::class)
                        ->group(function () {
                            Route::get('/',               'index')->name('index')->middleware('hasPermission:social_link_read');
                            Route::get('create',          'create')->name('create')->middleware('hasPermission:social_link_create');
                            Route::post('store',          'store')->name('store')->middleware('hasPermission:social_link_create');
                            Route::get('edit/{id}',       'edit')->name('edit')->middleware('hasPermission:social_link_update');
                            Route::put('update/{id}',     'update')->name('update')->middleware('hasPermission:social_link_update');
                            Route::delete('delete/{id}',  'delete')->name('delete')->middleware('hasPermission:social_link_delete');
                        });

                    Route::prefix('service')
                        ->name('service.')
                        ->controller(ServiceController::class)
                        ->group(function () {
                            Route::get('/',               'index')->name('index')->middleware('hasPermission:service_read');
                            Route::get('/outside',        'outsideCity')->name('outside')->middleware('hasPermission:service_read');
                            Route::get('create',          'create')->name('create')->middleware('hasPermission:service_create');
                            Route::post('store',          'store')->name('store')->middleware('hasPermission:service_create');
                            Route::get('edit/{id}',       'edit')->name('edit')->middleware('hasPermission:service_update');
                            Route::put('update/{id}',     'update')->name('update')->middleware('hasPermission:service_update');
                            Route::delete('delete/{id}',  'delete')->name('delete')->middleware('hasPermission:service_delete');
                            Route::get('shippingtypes',   'shippingtypes')->name('shippingtypes')->middleware('hasPermission:service_create');
                        });

                    Route::prefix('why-choose-us')
                        ->name('why.courier.')
                        ->controller(WhyCourierController::class)
                        ->group(function () {
                            Route::get('/',               'index')->name('index')->middleware('hasPermission:why_courier_read');
                            Route::get('create',          'create')->name('create')->middleware('hasPermission:why_courier_create');
                            Route::post('store',          'store')->name('store')->middleware('hasPermission:why_courier_create');
                            Route::get('edit/{id}',       'edit')->name('edit')->middleware('hasPermission:why_courier_update');
                            Route::put('update/{id}',     'update')->name('update')->middleware('hasPermission:why_courier_update');
                            Route::delete('delete/{id}',  'delete')->name('delete')->middleware('hasPermission:why_courier_delete');
                        });

                    Route::prefix('slider')
                        ->name('slider.')
                        ->controller(SliderController::class)
                        ->group(function () {
                            Route::get('/',               'index')->name('index')->middleware('hasPermission:slider_read');
                            Route::get('create',          'create')->name('create')->middleware('hasPermission:slider_create');
                            Route::post('store',          'store')->name('store')->middleware('hasPermission:slider_create');
                            Route::get('edit/{id}',       'edit')->name('edit')->middleware('hasPermission:slider_update');
                            Route::put('update/{id}',     'update')->name('update')->middleware('hasPermission:slider_update');
                            Route::delete('delete/{id}',  'delete')->name('delete')->middleware('hasPermission:slider_delete');
                        });

                    Route::prefix('faq')
                        ->name('faq.')
                        ->controller(FaqController::class)
                        ->group(function () {
                            Route::get('/',               'index')->name('index')->middleware('hasPermission:faq_read');
                            Route::get('create',          'create')->name('create')->middleware('hasPermission:faq_create');
                            Route::post('store',          'store')->name('store')->middleware('hasPermission:faq_create');
                            Route::get('edit/{id}',       'edit')->name('edit')->middleware('hasPermission:faq_update');
                            Route::put('update/{id}',     'update')->name('update')->middleware('hasPermission:faq_update');
                            Route::delete('delete/{id}',  'delete')->name('delete')->middleware('hasPermission:faq_delete');
                        });

                    Route::prefix('partner')
                        ->name('partner.')
                        ->controller(PartnerController::class)
                        ->group(function () {
                            Route::get('/',               'index')->name('index')->middleware('hasPermission:partner_read');
                            Route::get('create',          'create')->name('create')->middleware('hasPermission:partner_create');
                            Route::post('store',          'store')->name('store')->middleware('hasPermission:partner_create');
                            Route::get('edit/{id}',       'edit')->name('edit')->middleware('hasPermission:partner_update');
                            Route::put('update/{id}',     'update')->name('update')->middleware('hasPermission:partner_update');
                            Route::delete('delete/{id}',  'delete')->name('delete')->middleware('hasPermission:partner_delete');
                        });

                    Route::prefix('blogs')
                        ->name('blogs.')
                        ->controller(BlogController::class)
                        ->group(function () {
                            Route::get('/',               'index')->name('index')->middleware('hasPermission:blogs_read');
                            Route::get('create',          'create')->name('create')->middleware('hasPermission:blogs_create');
                            Route::post('store',          'store')->name('store')->middleware('hasPermission:blogs_create');
                            Route::get('edit/{id}',       'edit')->name('edit')->middleware('hasPermission:blogs_update');
                            Route::put('update/{id}',     'update')->name('update')->middleware('hasPermission:blogs_update');
                            Route::delete('delete/{id}',  'delete')->name('delete')->middleware('hasPermission:blogs_delete');
                        });

                    Route::prefix('pages')
                        ->name('pages.')
                        ->controller(PageController::class)
                        ->group(function () {
                            Route::get('/',               'index')->name('index')->middleware('hasPermission:pages_read');
                            Route::post('menu/update',    'updateMenu')->name('menu.update')->middleware('hasPermission:pages_update');
                            Route::get('edit/{id}',       'edit')->name('edit')->middleware('hasPermission:pages_update');
                            Route::put('update/{id}',     'update')->name('update')->middleware('hasPermission:pages_update');
                        });

                    Route::prefix('section')
                        ->name('section.')
                        ->controller(SectionController::class)
                        ->group(function () {
                            Route::get('/',               'index')->name('index')->middleware('hasPermission:section_read');
                            Route::get('edit/{type}',     'edit')->name('edit')->middleware('hasPermission:section_update');
                            Route::put('update/{type}',   'update')->name('update')->middleware('hasPermission:section_update');
                        });
                });



            });


            // Merchant panel Routes
            Route::group(['prefix' => 'sender'], function () {
                Route::group(['middleware' => 'merchantIsValid'], function () {
                    Route::get('analytics',                               [DashboardController::class, 'analytics'])->name('merchant.panel.analytics');
                    Route::post('/dashboard/filter',                      [DashboardController::class, 'merchantDashboardFilter'])->name('merchant-panel.dashboard.filter');
                    //Statistics
                    Route::get('statistics',                              [StatisticsController::class, 'merchantStatistics'])->name('merchant.panel.statistics');
                    //end Statistics

                    //accounts
                    Route::get('/accounts/payment-accounts',              [PaymentAccountController::class, 'index'])->name('merchant.accounts.payment-account.index');
                    Route::get('/accounts/payment-accounts/create',       [PaymentAccountController::class, 'create'])->name('payment.account.create');
                    Route::post('/accounts/payment-account/store',        [PaymentAccountController::class, 'store'])->name('payment.account.store');
                    Route::get('/accounts/payment-account/edit/{id}',     [PaymentAccountController::class, 'edit'])->name('payment.account.edit');
                    Route::put('/accounts/payment-account/update',        [PaymentAccountController::class, 'update'])->name('payment.account.update');
                    Route::delete('/accounts/payment-account/delete/{id}', [PaymentAccountController::class, 'delete'])->name('payment.account.delete');
                    // Account Transaction
                    Route::get('/accounts/account-transaction',         [AccountTransactionController::class, 'index'])->name('merchant.accounts.account-transaction.index');
                    Route::post('/accounts/account-transaction-filter', [AccountTransactionController::class, 'filter'])->name('merchant.accounts.account-transaction.filter');
                    // Statements
                    Route::get('/accounts/statements',                  [StatementsController::class, 'index'])->name('merchant.accounts.statements.index');
                    Route::post('/accounts/statements-filter',          [StatementsController::class, 'filter'])->name('merchant.accounts.statements.filter');
                    //settings
                    Route::get('/settings/cod-charges',      [SettingsController::class, 'CODcharges'])->name('merchant.cod-charges.index');
                    Route::get('/settings/delivery-charges', [SettingsController::class, 'deliveryCharges'])->name('merchant.delivery-charges.index');

                    // Merchant profile
                    Route::get('profile',                  [MerchantProfileController::class, 'view'])->name('merchant-profile.index');
                    Route::get('profile/update',           [MerchantProfileController::class, 'create'])->name('merchant-profile.edit');
                    Route::put('profile/update',           [MerchantProfileController::class, 'update'])->name('merchant-profile.update');
                    Route::get('profile/change-password',  [MerchantProfileController::class, 'changePassword'])->name('merchant-password.change');
                    Route::put('profile/update-password',  [MerchantProfileController::class, 'updatePassword'])->name('merchant-profile.password.update');
                    // Shops routes
                    Route::get('shops/index',            [ShopsController::class, 'index'])->name('merchant-panel.shops.index');
                    Route::get('shops/create',           [ShopsController::class, 'create'])->name('merchant-panel.shops.create');
                    Route::post('shops/store',           [ShopsController::class, 'store'])->name('merchant-panel.shops.store');
                    Route::get('shops/edit/{id}',        [ShopsController::class, 'edit'])->name('merchant-panel.shops.edit');
                    Route::put('shops/update/{id}',      [ShopsController::class, 'update'])->name('merchant-panel.shops.update');
                    Route::delete('shops/delete/{id}',   [ShopsController::class, 'delete'])->name('merchant-panel.shops.delete');
                    // Parcel Routes
                    Route::get('order/filter',          [MerchantParcelController::class, 'filter'])->name('merchant-panel.parcel.filter');
                    Route::get('order/index',           [MerchantParcelController::class, 'index'])->name('merchant-panel.parcel.index');
                    Route::get('order-bank/index',      [MerchantParcelController::class, 'parcelBank'])->name('merchant-panel.parcel-bank.index');
                    Route::get('order/create',          [MerchantParcelController::class, 'create'])->name('merchant-panel.parcel.create');
                    Route::post('order/store',          [MerchantParcelController::class, 'store'])->name('merchant-panel.parcel.store');
                    Route::get('order/clone/{id}',      [MerchantParcelController::class, 'duplicate'])->name('merchant-parcel.clone');
                    Route::post('order/clone-store',    [MerchantParcelController::class, 'duplicateStore'])->name('merchant-parcel.clone-store');
                    Route::get('order/edit/{id}',       [MerchantParcelController::class, 'edit'])->name('merchant-panel.parcel.edit');
                    Route::get('order/details/{id}',    [MerchantParcelController::class, 'details'])->name('merchant-panel.parcel.details');
                    Route::get('order/{id}/dispute',    [MerchantParcelDisputeController::class, 'create'])->name('merchant-panel.parcel.dispute.create');
                    Route::post('order/{id}/dispute',   [MerchantParcelDisputeController::class, 'store'])->name('merchant-panel.parcel.dispute.store');
                    Route::get('order/print/{id}',      [MerchantParcelController::class, 'parcelPrint'])->name('merchant-panel.parcel.print');
                    Route::get('order/logs/{id}',       [MerchantParcelController::class, 'logs'])->name('merchant-panel.parcel.logs');
                    Route::get('order/placed/{id}',     [MerchantParcelController::class,  'placed'])->name('merchant-panel.parcel.placed');
                    Route::put('order/update/{id}',     [MerchantParcelController::class, 'update'])->name('merchant-panel.parcel.update');
                    Route::get('order/status-update/{id}/{status_id}',   [MerchantParcelController::class, 'statusUpdate'])->name('merchant-panel.parcel.status-update');
                    Route::delete('order/delete/{id}',      [MerchantParcelController::class, 'destroy'])->name('merchant-panel.parcel.delete');
                    Route::post('parcel/merchant',          [MerchantParcelController::class, 'getMerchant'])->name('merchant-panel.parcel.merchant.get');
                    Route::post('parcel/merchant/shops',    [MerchantParcelController::class, 'merchantShops'])->name('merchant-panel.parcel.merchant.shops');
                    Route::post('parcel/delivery-category', [MerchantParcelController::class, 'deliveryWeight'])->name('merchant-panel.parcel.deliveryCategory.deliveryWeight');
                    Route::post('parcel/delivery-charge/unit/parcel',   [MerchantParcelController::class, 'deliveryChargeUnitParcel'])->name('merchant-panel.parcel.deliveryCharge.unitParcel.get');
                    Route::post('parcel/delivery-charge',   [MerchantParcelController::class, 'deliveryCharge'])->name('merchant-panel.parcel.deliveryCharge.get');

                    Route::get('active-live-monitoring',    [MerchantParcelController::class, 'activeLiveMonitoring'])->name('merchant-panel.active.live.monitoring');
                    Route::get('passive-monitoring',        [MerchantParcelController::class, 'passiveMonitoring'])->name('merchant-panel.passive.monitoring');
                    Route::get('monitoring-export',         [MerchantParcelController::class, 'parcelMonitoringExport'])->name('merchant-panel.monitoring.export');
                    //receiver
                    Route::get('received/parcels',          [MerchantParcelController::class, 'recievedParcels'])->name('merchant-panel.received.parcel.index');
                    //import
                    Route::get('order/import-parcel',       [MerchantParcelController::class, 'parcelImportExport'])->name('merchant-panel.parcel.parcel-import');
                    Route::post('parcel/file-import',       [MerchantParcelController::class, 'parcelImport'])->name('merchant-panel.parcel.file-import');
                    Route::get('order/file-export',         [MerchantParcelController::class, 'parcelExport'])->name('merchant-panel.parcel.file-export');


                    Route::get('customers/index',           [SenderPanelCustomerController::class, 'index'])->name('merchant-panel.customers.index');
                    Route::get('customers/create',          [SenderPanelCustomerController::class, 'create'])->name('merchant-panel.customers.create');
                    Route::post('customers/store',          [SenderPanelCustomerController::class, 'store'])->name('merchant-panel.customers.store');
                    Route::get('customers/edit/{id}',       [SenderPanelCustomerController::class, 'edit'])->name('merchant-panel.customers.edit');
                    Route::put('customers/update/{id}',     [SenderPanelCustomerController::class, 'update'])->name('merchant-panel.customers.update');
                    Route::delete('customers/delete/{id}',  [SenderPanelCustomerController::class, 'destroy'])->name('merchant-panel.customers.delete');
                    Route::get('customers/show/{id}',       [SenderPanelCustomerController::class, 'show'])->name('merchant-panel.customers.view');

                    Route::get('reports/order-reports',                [MerchantReportsController::class, 'parcelReports'])->name('merchant-panel.parcel.reports');
                    Route::get('reports/order-filter-reports',         [MerchantReportsController::class, 'parcelSReports'])->name('merchant-panel.parcel.filter.reports');
                    Route::get('order-reports-print-page/{array}',     [MerchantReportsController::class, 'parcelReportsPrint'])->name('merchant-panel.parcel.reports.print.page');
                    //payment request
                    Route::get('payment-request/index',         [PaymentRequestController::class, 'index'])->name('merchant-panel.payment-request.index');
                    Route::get('payment-request/create',        [PaymentRequestController::class, 'create'])->name('merchant-panel.payment-request.create');
                    Route::post('payment-request/store',        [PaymentRequestController::class, 'store'])->name('merchant-panel.payment-request.store');
                    Route::get('payment-request/edit/{id}',     [PaymentRequestController::class, 'edit'])->name('merchant-panel.payment-request.edit');
                    Route::put('payment-request/update',        [PaymentRequestController::class, 'update'])->name('merchant-panel.payment-request.update');
                    Route::delete('payment-request/delete/{id}', [PaymentRequestController::class, 'delete'])->name('merchant-panel.payment-request.delete');
                    // News & Offer
                    // Support
                    Route::get('support/index',          [MerchantPanelSupportController::class, 'index'])->name('merchant-panel.support.index');
                    Route::get('support/create',         [MerchantPanelSupportController::class, 'create'])->name('merchant-panel.support.add');
                    Route::post('support/store',         [MerchantPanelSupportController::class, 'store'])->name('merchant-panel.support.store');
                    Route::get('support/edit/{id}',      [MerchantPanelSupportController::class, 'edit'])->name('merchant-panel.support.edit');
                    Route::put('support/update/{id}',    [MerchantPanelSupportController::class, 'update'])->name('merchant-panel.support.update');
                    Route::delete('support/delete/{id}', [MerchantPanelSupportController::class, 'destroy'])->name('merchant-panel.support.delete');
                    Route::get('support/view/{id}',      [MerchantPanelSupportController::class, 'view'])->name('merchant-panel.support.view');
                    Route::post('support/reply',         [MerchantPanelSupportController::class, 'supportReply'])->name('merchant-panel.support.reply');
                    Route::post('mpesa/pay',             [MerchantPanelMpesaController::class, 'pay'])->name('merchant-panel.mpesa.pay');
                    Route::get('mpesa/pay/parcel/{parcel}',  [MerchantPanelMpesaController::class, 'payParcelForm'])->name('merchant-panel.mpesa.pay.parcel.form');
                    Route::post('mpesa/pay/parcel/{parcel}', [MerchantPanelMpesaController::class, 'payParcel'])->name('merchant-panel.mpesa.pay.parcel');
                    Route::get('mpesa/payment-history',  [MpesaPaymentHistoryController::class, 'index'])->name('merchant-panel.mpesa.payment-history.index');
                    // Fraud
                    Route::get('deception',                [MerchantPanelFraudController::class, 'index'])->name('merchant-panel.fraud.index');
                    Route::get('deception/create',         [MerchantPanelFraudController::class, 'create'])->name('merchant-panel.fraud.create');
                    Route::post('deception/store',         [MerchantPanelFraudController::class, 'store'])->name('merchant-panel.fraud.store');
                    Route::get('deception/edit/{id}',      [MerchantPanelFraudController::class, 'edit'])->name('merchant-panel.fraud.edit');
                    Route::put('deception/update',         [MerchantPanelFraudController::class, 'update'])->name('merchant-panel.fraud.update');
                    Route::delete('deception/delete/{id}', [MerchantPanelFraudController::class, 'destroy'])->name('merchant-panel.fraud.delete');
                    Route::get('deception/filter',         [MerchantPanelFraudController::class, 'filter'])->name('merchant-panel.fraud.filter');
                    Route::post('deception/check',         [MerchantPanelFraudController::class, 'check'])->name('merchant-panel.fraud.check');
                    //reports
                    Route::get('reports/total-summery',            [MerchantPanelReportsController::class, 'TotalSummeryReports'])->name('merchant.total.summery');
                    Route::get('reports/total-summery-filter',     [MerchantPanelReportsController::class, 'TotalSummeryReportsFilter'])->name('merchant.parcel.filter.total.summery');
                    //pickup request
                    Route::prefix('pickup-request')->name('merchant.panel.pickup.request.')->group(function () {
                        Route::post('regular',                      [MerchantPanelPickupRequestController::class, 'regularStore'])->name('regular.store');
                        Route::post('express',                      [MerchantPanelPickupRequestController::class, 'expressStore'])->name('express.store');
                    });
                    Route::prefix('invoice')->name('merchant.panel.invoice.')->group(function () {
                        Route::get('/',                             [InvoiceController::class, 'index'])->name('index');
                        Route::get('/{invoice_id}',                  [InvoiceController::class, 'InvoiceDetails'])->name('details');
                        Route::get('/pdf/{merchant_id}/{invoice_id}', [MerchantInvoiceController::class, 'InvoicePdf'])->name('pdf');
                        Route::get('/csv/{merchant_id}/{invoice_id}', [MerchantInvoiceController::class, 'InvoiceCSV'])->name('csv');
                    });
                    //merchant online payment  received setup
                    Route::get('/settings/online-payment-setup',                            [MerchantOnlinePaymentSetupController::class, 'index'])->name('merchant.online.payment.setup.index');
                    Route::put('/settings/online-payment-setup/update/{paymentmethod}',     [MerchantOnlinePaymentSetupController::class, 'paymentReceivedSetupUpdate'])->name('merchant.online.payment.setup.update');
                    Route::get('online-payment-received-list',                              [MerchantOnlinePaymentSetupController::class, 'onlinePaymentReceivedList'])->name('merchant.online.payment.list');
                    //online payment module
                    Route::get('/payment/received',                            [OnlinePaymentController::class, 'merchantPaymentReceived'])->name('online.payment.received');
                    Route::prefix('online-payment')->name('online.payment.')->group(function () {
                        //stripe payment gateway
                        Route::get('/',                                     [OnlinePaymentController::class, 'index'])->name('index');
                        Route::get('/stripe',                               [OnlinePaymentController::class, 'stripe'])->name('stripe');
                        Route::post('/stripe/post',                         [OnlinePaymentController::class, 'stripePost'])->name('stripe.post');
                        //paypal payment gateway
                        Route::get('paypal-index',                         [OnlinePaymentController::class, 'paypalIndex'])->name('paypal.index');
                        Route::post('paypal-payment',                      [OnlinePaymentController::class, 'paypalpayment'])->name('paypal');
                        //ssl commerz
                        Route::get('/sslcommerz',                          [OnlinePaymentController::class, 'sslcommerzIndex'])->name('sslcommerz.index');
                        Route::get('/aamarpay',                            [OnlinePaymentController::class, 'aamarpayIndex'])->name('aamarpay.index');
                    });

                });
            });
            // SSLCOMMERZ Start
            Route::post('/pay-via-ajax',              [SslCommerzPaymentController::class, 'payViaAjax']);
            Route::post('/success',                   [SslCommerzPaymentController::class, 'success']);
            Route::post('/fail',                      [SslCommerzPaymentController::class, 'fail']);
            Route::post('/cancel',                    [SslCommerzPaymentController::class, 'cancel']);
            Route::post('/ipn',                       [SslCommerzPaymentController::class, 'ipn']);
            //skrill payment start
            Route::get('skrill',                      [SkrillController::class, 'index'])->name('skrill.index');
            Route::get('skrill-make-payment',         [SkrillController::class, 'makePayment'])->name('skrill.make.payment');
            Route::get('payment-completed',           [SkrillController::class, 'paymentCompleted'])->name('skrill.payment.completed');
            Route::get('payment-cancelled',           [SkrillController::class, 'PaymentCancelled']);
            //bkash payment
            Route::get('/online-payment/bkash',       [BkashController::class, 'index'])->name('online.payment.bkash.index');
            Route::get('bkash/redirect',              [BkashController::class, 'bkashRedirect'])->name('bkash.redirect');
            Route::get('bkash/execute',               [BkashController::class, 'bkashExecute'])->name('bkash.execute');
            //amarpay
            Route::get('/aamarpay-payment',           [AamarpayController::class, 'payment'])->name('aamarpay.payment');
            Route::post('/aamarpay-success',          [AamarpayController::class, 'success'])->name('aamarpay.payment.success');
            Route::post('/aamarpay-fail',             [AamarpayController::class, 'fail'])->name('aamarpay.payment.fail');

            //delivery man panel route
            Route::get('deliveryman/parcel/accept/{id}',       [ParcelController::class, 'deliverymanParcelAccept'])->name('deliveryman.parcel.accept');
            Route::get('deliveryman/parcel/logs/{id}',         [ParcelController::class, 'logs'])->name('deliveryman.parcel.logs');
            Route::get('deliveryman/parcel/status/update/page', [ParcelController::class, 'parcelStatusUpdatePage'])->name('deliveryman.parcel.status.update.page');
            Route::post('deliveryman/parcel/status-update',    [ParcelController::class, 'deliverymanParcelStatusUpdate'])->name('deliveryman.parcel.status.update');
        });
        // Theme Pages

        // FCM Token
        Route::post('/store-token', [WebNotificationController::class, 'store'])->name('notification-store.token');
    });

    Route::group(['middleware' => ['auth']], function () {
        Route::get('email/verify/{token}', [VerificationController::class, 'emailVerify'])->name('email.verify');
        Route::get('resend/email/verify', [VerificationController::class, 'resendEmailVerify'])->name('resend.email.verify');
        Route::get('phone/verify', [VerificationController::class, 'phoneVerify'])->name('phone.verify');
        Route::get('verify/phone', [VerificationController::class, 'verifyPhone'])->name('verify.phone');
        Route::post('customer/resend/otp', [VerificationController::class, 'merchantResendOtp'])->name('customer.resend_otp');
        Route::post('customer/verify/phone', [VerificationController::class, 'merchantVerifyPhone'])->name('customer.verify.phone');
    });

    Route::get('get-a-qoute',                   [MerchantParcelController::class, 'getQoute'])->name('get-qoute');
});
