<!-- left sidebar -->
<div class="col-12 nav-left-sidebar sidebar-dark">
    @php
        $isDashboard = request()->is('dashboard*');
        $isOrderManagement = request()->is('admin/order*', 'admin/parcel/specific/search*', 'admin/return/report*', 'admin/statistics*', 'admin/disputes*');
        $isUserManagement = request()->is('admin/customer*', 'admin/driver*', 'admin/roles*', 'admin/users*', 'admin/designations*', 'admin/departments*');
        $isRiderKycReview = request()->is('admin/driver/kyc-review');
        $isRiderWallets = request()->is('admin/rider-wallets*');
        $isRidersMenu = request()->is('admin/driver*') && !$isRiderKycReview;
        $isDataAnalytics = request()->is('admin/push-notification*', 'admin/addons*', 'admin/analytics', 'admin/reports/*', 'admin/attendance/report*', 'admin/leave/report*');
        $isFinance = request()->is(
            'admin/accounts*',
            'admin/fund-transfer*',
            'admin/expense*',
            'admin/income*',
            'admin/account-head*',
            'admin/bank-transaction*',
            'admin/paid/invoice*',
            'admin/online-payment-list*',
            'admin/payout*',
            'admin/platform-ledger*',
            'admin/salary/salary-generate*',
            'admin/salary*'
        );
        $isOrderCreate = request()->is('admin/order/create');
        $isOrderMonitoring = request()->is('admin/order*') || request()->is('admin/parcel/specific/search*');
        $isReturnReport = request()->is('admin/return/report*');
        $isStatistics = request()->is('admin/statistics*');
        $isDisputes = request()->is('admin/disputes*');
        $isCustomer = request()->is('admin/customer*');
        $isRoles = request()->is('admin/roles*');
        $isDesignations = request()->is('admin/designations*');
        $isDepartments = request()->is('admin/departments*');
        $isUsers = request()->is('admin/users*');
        $isDeliveryCharge = request()->is('admin/delivery-charge*');
        $isParcelReports = request()->is('admin/reports/parcel-reports*', 'admin/reports/parcel-filter-reports');
        $isRiderOverviewReport = request()->is('admin/reports/rider-overview*');
        $isMarketplaceEarnings = request()->is('admin/reports/marketplace-earnings*');
        $isRiderEarningsReport = request()->is('admin/reports/rider-earnings*');
        $isCompletedDeliveries = request()->is('admin/reports/completed-deliveries*');
        $isTotalRevenue = request()->is('admin/reports/total-revenue*');
        $isAnalyticsReport = request()->is('admin/analytics*');
        $isPushNotification = request()->is('admin/push-notification*');
        $isSalaryMenu = request()->is('admin/salary/salary-generate*', 'admin/salary*');
        $isSalaryGenerate = request()->is('admin/salary/salary-generate*', 'admin/reports/parcel-filter-reports');
        $isSalary = request()->is('admin/salarys*');
        $isAccountHeads = request()->is('admin/account-heads*');
        $isAccounts = request()->is('admin/accounts*');
        $isFundTransfer = request()->is('admin/fund-transfer*');
        $isIncome = request()->is('admin/income*');
        $isExpense = request()->is('admin/expense*');
        $isBankTransaction = request()->is('admin/bank-transaction*');
        $isPaidInvoice = request()->is('admin/paid/invoice*');
        $isOnlinePaymentList = request()->is('admin/online-payment-list*');
        $isPayout = request()->is('admin/payout*');
        $isPlatformLedger = request()->is('admin/platform-ledger*');
        $isRiderWithdrawRequests = request()->is('admin/rider-withdraw-requests*');
        $isWebsiteSetup = request()->is('admin/front-web*');
        $isFrontWebSlider = request()->is('admin/front-web/slider*');
        $isFrontWebSocialLink = request()->is('admin/front-web/social-link*');
        $isFrontWebService = request()->is('admin/front-web/service*');
        $isFrontWebWhyCourier = request()->is('admin/front-web/why-choose-us*');
        $isFrontWebFaq = request()->is('admin/front-web/faq*');
        $isFrontWebPartner = request()->is('admin/front-web/partner*');
        $isFrontWebPages = request()->is('admin/front-web/pages*');
        $isFrontWebSection = request()->is('admin/front-web/section*');
        $isSupportMenu = request()->is('admin/support*', 'admin/deception*');
        $isSupport = request()->is('admin/support*');
        $isSettingsMenu = request()->is(
            'admin/shipping-type*',
            'admin/database-backup*',
            'admin/delivery-category*',
            'admin/packaging*',
            'admin/delivery-type*',
            'admin/liquid-fragile*',
            'admin/extra_cost*',
            'admin/sms-settings*',
            'admin/sms-send-settings*',
            'admin/general-settings*',
            'admin/mail-settings*',
            'admin/notification-settings*',
            'admin/asset-category*',
            'admin/social-login-setting*',
            'admin/pay-out/setup*',
            'admin/settings/pay-out/setup*',
            'admin/settings/invoice-generate-menually*',
            'admin/currency*',
            'admin/countries*',
            'admin/cities*',
            'admin/districts*',
            'admin/towns*',
            'admin/zone-delivery-charge*',
            'admin/parcel-category*',
            'admin/province*'
        );
        $isGeneralSettings = request()->is('admin/general-settings*');
        $isMailSettings = request()->is('admin/mail-settings*');
        $isShippingType = request()->is('admin/shipping-type*');
        $isLiquidFragile = request()->is('admin/liquid-fragile*');
        $isExtraCost = request()->is('admin/extra_cost*');
        $isSmsSettings = request()->is('admin/sms-settings*');
        $isSmsSendSettings = request()->is('admin/sms-send-settings*');
        $isProvince = request()->is('admin/province*');
        $isCities = request()->is('admin/cities*');
        $isNotificationSettings = request()->is('admin/notification-settings*');
        $isPayoutSetupSettings = request()->is('admin/settings/pay-out/setup*');
        $isPackaging = request()->is('admin/packaging*');
        $isCurrency = request()->is('admin/currency*');
        $isParcelCategory = request()->is('admin/parcel-category*');
        $isInvoiceGenerateMenually = request()->is('admin/settings/invoice-generate-menually*');
    @endphp
    <ul class="navbar-nav">
        <!-- <li class="nav-divider">
            {{ __('menus.menu') }}
        </li> -->
        <li class="nav-item">
            @if (hasPermission('dashboard_read') == true)
            <a class="nav-link {{ $isDashboard ? 'active' : '' }}" href="{{ url('/dashboard') }}"
                aria-expanded="false" data-target="#submenu-1" aria-controls="submenu-1">
                <i class="fa fa-home"></i>{{ __('menus.dashboard') }}
            </a>
            @endif
        </li>

        <li class="nav-item">
            <a class="nav-link {{ $isOrderManagement ? 'active' : '' }}"
                href="#" data-toggle="collapse"
                aria-expanded="{{ $isOrderManagement ? 'true' : 'false' }}"
                data-target="#order-management" aria-controls="order-management"><i class="fa fa-cube"></i>{{ __('menus.order_management') }}</a>
            <div id="order-management"
                class="{{ $isOrderManagement ? '' : 'collapse' }} submenu">
                <ul class="nav flex-column">
                    @if (hasPermission('parcel_create') == true)
                    <li class="nav-item ">
                        <a class="nav-link {{ $isOrderCreate ? 'active' : '' }}"
                            href="{{ route('parcel.create') }}" aria-expanded="false" data-target="#submenu-1"
                            aria-controls="submenu-1">
                            {{ __('menus.create_order') }}
                        </a>
                    </li>
                    @endif
                    @if (hasPermission('parcel_read') == true)
                    <li class="nav-item ">
                        <a class="nav-link {{ $isOrderMonitoring ? 'active' : '' }}"
                            href="{{ route('parcel.index') }}" aria-expanded="false" data-target="#submenu-1"
                            aria-controls="submenu-1">
                            {{ __('menus.order_monitoring') }}
                        </a>
                    </li>
                    @endif
                    @if (hasPermission('dispute_read') == true)
                    <li class="nav-item">
                        <a class="nav-link {{ $isDisputes ? 'active' : '' }}"
                            href="{{ route('admin.disputes.index') }}" aria-expanded="false" data-target="#submenu-1"
                            aria-controls="submenu-1">
                            Disputes
                        </a>
                    </li>
                    @endif
                    <!-- @if (hasPermission('return_report_read') == true)
                    <li class="nav-item">
                        <a class="nav-link {{ $isReturnReport ? 'active' : '' }}"
                            href="{{ route('parcel.return_report') }}" aria-expanded="false"
                            data-target="#submenu-1" aria-controls="submenu-1">
                            {{ __('menus.return_report') }}
                        </a>
                    </li>
                    @endif -->
                    <!-- @if (hasPermission('statistics_read'))
                    <li class="nav-item">
                        <a class="nav-link {{ $isStatistics ? 'active' : '' }}"
                            aria-expanded="false" data-target="#submenu-1" aria-controls="submenu-1"
                            href="{{ route('admin.statistics') }}">
                            {{ __('menus.order_history_and_analytics') }}
                        </a>
                    </li>
                    @endif -->
                </ul>
            </div>
        </li>
        @if (hasPermission('customer_read') == true ||
        hasPermission('delivery_man_read') == true ||
        hasPermission('role_read') == true ||
        hasPermission('designation_read') == true ||
        hasPermission('department_read') == true ||
        hasPermission('user_read') == true)
        <li class="nav-item">
            <a class="nav-link {{ $isUserManagement ? 'active' : '' }} "
                href="#" data-toggle="collapse"
                aria-expanded="{{ $isUserManagement ? 'true' : 'false' }} "
                data-target="#user-management" aria-controls="user-management">
                <i class="fas fa-users"></i>
                User Management</a>
            <div id="user-management"
                class="{{ $isUserManagement ? '' : 'collapse' }} submenu">
                <ul class="nav flex-column">
                    @if (hasPermission('customer_read') == true)
                    <li class="nav-item ">
                        <a class="nav-link {{ $isCustomer ? 'active' : '' }}"
                            href="{{ route('customer.index') }}" aria-expanded="false" data-target="#submenu-1"
                            aria-controls="submenu-1">
                            {{ __('menus.sender') }}
                        </a>
                    </li>
                    @endif
                    @if (hasPermission('delivery_man_read') == true)
                    <li class="nav-item">
                        <a class="nav-link {{ $isRidersMenu ? 'active' : '' }}"
                            href="{{ route('deliveryman.index') }}" aria-expanded="false" data-target="#submenu-1"
                            aria-controls="submenu-1">
                            Riders
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $isRiderKycReview ? 'active' : '' }}"
                            href="{{ route('deliveryman.kyc.index') }}" aria-expanded="false" data-target="#submenu-1"
                            aria-controls="submenu-1">
                            Rider KYC Review
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $isRiderWallets ? 'active' : '' }}"
                            href="{{ route('rider.wallets.index') }}" aria-expanded="false" data-target="#submenu-1"
                            aria-controls="submenu-1">
                            Rider Wallets
                        </a>
                    </li>
                    @endif
                    @if (hasPermission('role_read') == true)
                    <li class="nav-item">
                        <a class="nav-link {{ $isRoles ? 'active' : '' }}"
                            href="{{ route('roles.index') }}">{{ __('menus.roles') }}</a>
                    </li>
                    @endif
                    @if (hasPermission('designation_read') == true)
                    <li class="nav-item">
                        <a class="nav-link {{ $isDesignations ? 'active' : '' }}"
                            href="{{ route('designations.index') }}">{{ __('menus.designations') }}</a>
                    </li>
                    @endif
                    @if (hasPermission('department_read') == true)
                    <li class="nav-item">
                        <a class="nav-link {{ $isDepartments ? 'active' : '' }}"
                            href="{{ route('departments.index') }}">{{ __('menus.departments') }}</a>
                    </li>
                    @endif
                    @if (hasPermission('user_read') == true)
                    <li class="nav-item">
                        <a class="nav-link {{ $isUsers ? 'active' : '' }}"
                            href="{{ route('users.index') }}">{{ __('menus.users') }}</a>
                    </li>
                    @endif
                </ul>
            </div>
        </li>
        @endif
        <!-- <li class="nav-item">
            <a class="nav-link {{ request()->is('admin/request/dispatcher/payment*', 'admin/dispatcher/incharge*', 'admin/dispatcher/view*', 'admin/request/dispatcher/payment*', 'admin/payment*', 'admin/pickup-request*', 'admin/assets*', 'admin/vehicles*') ? 'active' : '' }}"
                href="#" data-toggle="collapse"
                aria-expanded="{{ request()->is('admin/request/dispatcher/payment*', 'admin/dispatcher/incharge*', 'admin/dispatcher/view*', 'admin/request/dispatcher/payment*', 'admin/payment*', 'admin/pickup-request*', 'admin/assets*', 'admin/vehicles*') ? 'true' : 'false' }}"
                data-target="#courier-management" aria-controls="courier-management"><i
                    class="fas fa-warehouse"></i>{{ __('menus.courier_management') }}</a>
            <div id="courier-management"
                class="{{ request()->is('admin/request/dispatcher/payment*', 'admin/dispatcher/incharge*', 'admin/dispatcher/view*', 'admin/request/dispatcher/payment*', 'admin/payment*', 'admin/pickup-request*', 'admin/assets*', 'admin/vehicles*') ? '' : 'collapse' }} submenu">
                <ul class="nav flex-column">
                    @if (hasPermission('payment_read') == true)
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('admin/payment*') ? 'active' : '' }}"
                            href="{{ route('merchant.manage.payment.index') }}" data-target="#merchant-manage"
                            aria-controls="merchant-manage">
                            {{ __('menus.sender_payments') }}
                        </a>
                    </li>
                    @endif
                    @if (hasPermission('pickup_request_regular') == true || hasPermission('pickup_request_express') == true)
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('admin/pickup-request*') ? 'active' : '' }}"
                            href="#" data-toggle="collapse"
                            aria-expanded="{{ request()->is('admin/pickup-request*') ? 'true' : 'false' }}"
                            data-target="#pickup-requested" aria-controls="hub-manage">
                            {{ __('menus.pickup_request') }}</a>
                        <div id="pickup-requested"
                            class="{{ request()->is('admin/pickup-request*') ? '' : 'collapse' }} submenu">
                            <ul class="nav flex-column">
                                @if (hasPermission('pickup_request_regular') == true)
                                <li class="nav-item ">
                                    <a class="nav-link {{ request()->is('admin/pickup-request/regular*') ? 'active' : '' }}"
                                        href="{{ route('pickup.request.regular') }}">{{ __('menus.regular') }}</a>
                                </li>
                                @endif
                                @if (hasPermission('pickup_request_express') == true)
                                <li class="nav-item ">
                                    <a class="nav-link {{ request()->is('admin/pickup-request/express*') ? 'active' : '' }}"
                                        href="{{ route('pickup.request.express') }}">{{ __('menus.express') }}</a>
                                </li>
                                @endif
                            </ul>
                        </div>
                    </li>
                    @endif
                    @if (hasPermission('vehicle_read') == true)
                    <li class="nav-item ">
                        <a class="nav-link {{ request()->is('admin/vehicles*') ? 'active' : '' }}"
                            href="{{ route('vehicles.index') }}" aria-expanded="false" data-target="#hubs"
                            aria-controls="hubs">
                            {{ __('menus.vehicle') }}
                        </a>
                    </li>
                    @endif
                </ul>
            </div>
        </li> -->
        @if (hasPermission('delivery_charge_read') == true)
        <li class="nav-item">
            <a class="nav-link {{ $isDeliveryCharge ? 'active' : '' }}"
                href="{{ route('delivery-charge.index') }}">
                <i class="fa fa-money-bill"></i>Delivery Charges
            </a>
        </li>
        @endif
        <li class="nav-item">
            <a class="nav-link {{ $isDataAnalytics ? 'active' : '' }}"
                href="#" data-toggle="collapse"
                aria-expanded="{{ $isDataAnalytics ? 'true' : 'false' }}"
                data-target="#data-analytics" aria-controls="data-analytics"><i
                    class="fa fa-chart-line"></i>{{ __('menus.data_analytics_statastics') }}</a>
            <div id="data-analytics"
                class="{{ $isDataAnalytics ? '' : 'collapse' }} submenu">
                <ul class="nav flex-column">
                    @if (hasPermission('parcel_status_reports') == true)
                    <li class="nav-item ">
                        <a class="nav-link {{ $isParcelReports ? 'active' : '' }}"
                            href="{{ route('parcel.reports') }}" aria-expanded="false"
                            data-target="#submenu-1"
                            aria-controls="submenu-1">{{ __('reports.parcel_reports') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $isRiderOverviewReport ? 'active' : '' }}"
                            href="{{ route('rider.overview') }}">Rider Overview</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $isMarketplaceEarnings ? 'active' : '' }}"
                            href="{{ route('marketplace.earnings') }}">Marketplace Earnings</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $isRiderEarningsReport ? 'active' : '' }}"
                            href="{{ route('rider.earnings') }}">Rider Earnings</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $isCompletedDeliveries ? 'active' : '' }}"
                            href="{{ route('reports.completed.deliveries') }}">Total Completed Deliveries</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $isTotalRevenue ? 'active' : '' }}"
                            href="{{ route('parcel.total.summery.index') }}">Total Revenue</a>
                    </li>
                    @endif
                    <!-- @if (hasPermission('analytics_read'))
                    <li class="nav-item ">
                        <a class="nav-link {{ $isAnalyticsReport ? 'active' : '' }}"
                            href="{{ route('admin.analytics') }}">
                            {{ __('parcel.analytics') }}</a>
                    </li>
                    @endif
                    @if (hasPermission('push_notification_read') == true)
                    <li class="nav-item ">
                        <a class="nav-link {{ $isPushNotification ? 'active' : '' }}"
                            href="{{ route('push-notification.index') }}" aria-expanded="false"
                            data-target="#submenu-1" aria-controls="submenu-1">
                            {{ __('menus.push_notification') }}</a>
                    </li>
                    @endif -->
                </ul>
            </div>
        </li>




        @if (hasPermission('account_read') == true ||
        hasPermission('fund_transfer_read') == true ||
        hasPermission('salary_generate_read') == true ||
        hasPermission('salary_read') == true ||
        hasPermission('cash_received_from_delivery_man_read') == true ||
        hasPermission('account_heads_read') == true ||
        hasPermission('income_read') == true ||
        hasPermission('expense_read') == true ||
        hasPermission('bank_transaction_read') == true ||
        hasPermission('paid_invoice_read') == true ||
        hasPermission('online_payment_read') == true ||
        hasPermission('payout_read') == true ||
        hasPermission('platform_ledger_read') == true)
        <li class="nav-item">
            <a class="nav-link {{ $isFinance ? 'active' : '' }} "
                href="#" data-toggle="collapse"
                aria-expanded="{{ $isFinance ? 'true' : 'false' }}"
                data-target="#account" aria-controls="account">
                <i class="fa fa-sack-dollar"></i>
                {{ __('menus.finance_and_accounting') }}</a>
            <div id="account"
                class="{{ $isFinance ? '' : 'collapse' }} submenu">
                <ul class="nav flex-column">

                    <!-- @if (hasPermission('salary_generate_read') == true || hasPermission('salary_read') == true)
                    <li class="nav-item">
                        <a class="nav-link {{ $isSalaryMenu ? 'active' : '' }}"
                            href="#" data-toggle="collapse"
                            aria-expanded="{{ $isSalaryMenu ? 'true' : 'false' }}"
                            data-target="#salarygenerate" aria-controls="salarygenerate">

                            {{ __('salary.payroll') }}</a>
                        <div id="salarygenerate"
                            class="{{ $isSalaryMenu ? '' : 'collapse' }} submenu">
                            <ul class="nav flex-column">
                                @if (hasPermission('salary_generate_read') == true)
                                <li class="nav-item ">
                                    <a class="nav-link {{ $isSalaryGenerate ? 'active' : '' }}"
                                        href="{{ route('salary.generate.index') }}"
                                        aria-expanded="false" data-target="#submenu-1"
                                        aria-controls="submenu-1">{{ __('salary.salary_generate') }}</a>
                                </li>
                                @endif

                                @if (hasPermission('salary_read') == true)
                                <li class="nav-item">
                                    <a class="nav-link {{ $isSalary ? 'active' : '' }}"
                                        href="{{ route('salary.index') }}">{{ __('menus.salary') }}</a>
                                </li>
                                @endif
                            </ul>
                        </div>
                    </li>
                    @endif -->


                    <!-- @if (hasPermission('account_heads_read') == true)
                    <li class="nav-item">
                        <a class="nav-link {{ $isAccountHeads ? 'active' : '' }}"
                            href="{{ route('account.heads.index') }}">{{ __('menus.account_heads') }}</a>
                    </li>
                    @endif

                    @if (hasPermission('account_read') == true)
                    <li class="nav-item">
                        <a class="nav-link {{ $isAccounts ? 'active' : '' }}"
                            href="{{ route('accounts.index') }}">{{ __('menus.accounts') }}</a>
                    </li>
                    @endif
                    @if (hasPermission('fund_transfer_read') == true)
                    <li class="nav-item">
                        <a class="nav-link {{ $isFundTransfer ? 'active' : '' }}"
                            href="{{ route('fund-transfer.index') }}">{{ __('menus.fund_transfer') }}</a>
                    </li>
                    @endif
                    @if (hasPermission('income_read') == true)
                    <li class="nav-item">
                        <a class="nav-link {{ $isIncome ? 'active' : '' }}"
                            href="{{ route('income.index') }}">{{ __('menus.income') }}</a>
                    </li>
                    @endif
                    @if (hasPermission('expense_read') == true)
                    <li class="nav-item">
                        <a class="nav-link {{ $isExpense ? 'active' : '' }}"
                            href="{{ route('expense.index') }}">{{ __('menus.expense') }}</a>
                    </li>
                    @endif -->

                    @if (hasPermission('platform_ledger_read') == true)
                    <li class="nav-item">
                        <a class="nav-link {{ $isPlatformLedger ? 'active' : '' }}"
                            href="{{ route('admin.platform-ledger.index') }}">Platform Ledger</a>
                    </li>
                    @endif
                    <!-- @if (hasPermission('bank_transaction_read') == true)
                    <li class="nav-item">
                        <a class="nav-link {{ $isBankTransaction ? 'active' : '' }}"
                            href="{{ route('bank-transaction.index') }}">{{ __('menus.bank_transaction') }}</a>
                    </li>
                    @endif

                    @if (hasPermission('paid_invoice_read') == true)
                    <li class="nav-item">
                        <a class="nav-link {{ $isPaidInvoice ? 'active' : '' }}"
                            href="{{ route('paid.invoice.index') }}">{{ __('invoice.paid_invoice') }}</a>
                    </li>
                    @endif


                    @if (hasPermission('online_payment_read') == true)
                    <li class="nav-item ">
                        <a class="nav-link {{ $isOnlinePaymentList ? 'active' : '' }}"
                            href="{{ route('online.payment.list') }}">

                            {{ __('menus.payments_received') }}</a>
                    </li>
                    @endif

                    @if (hasPermission('payout_read') == true)
                    <li class="nav-item ">
                        <a class="nav-link {{ $isPayout ? 'active' : '' }}"
                            href="{{ route('payout.index') }}">

                            {{ __('menus.payout') }}</a>
                    </li>
                    @endif -->
                    <li class="nav-item ">
                        <a class="nav-link {{ $isRiderWithdrawRequests ? 'active' : '' }}"
                            href="{{ route('rider.withdraw.requests.index') }}">
                            Rider Withdraw Requests</a>
                    </li>


                </ul>
            </div>
        </li>
        @endif


        @if (hasPermission('support_read') == true || hasPermission('fraud_read') == true)
        <li class="nav-item">
            <a class="nav-link {{ $isSupportMenu ? 'active' : '' }}"
                href="#" data-toggle="collapse" aria-expanded="false" data-target="#pickup-request"
                aria-controls="hub-manage"><i
                    class="fa-solid fa-headset"></i>{{ __('menus.customer_support') }}</a>
            <div id="pickup-request"
                class="{{ $isSupportMenu ? '' : 'collapse' }} submenu">
                <ul class="nav flex-column">
                    @if (hasPermission('support_read') == true)
                    <li class="nav-item ">
                        <a class="nav-link {{ $isSupport ? 'active' : '' }}"
                            href="{{ route('support.index') }}" aria-expanded="false" data-target="#hubs"
                            aria-controls="hubs">{{ __('menus.support') }}</a>
                    </li>
                    @endif
                    {{-- Fraud --}}



                </ul>
            </div>
        </li>
        @endif














        @if (
            hasPermission('social_link_read') == true ||
            hasPermission('service_read') == true ||
            hasPermission('why_courier_read') == true ||
            hasPermission('slider_read') == true ||
            hasPermission('faq_read') == true ||
            hasPermission('partner_read') == true ||
            hasPermission('pages_read') == true ||
            hasPermission('section_read') == true
        )
        <li class="nav-item">
            <a class="nav-link {{ $isWebsiteSetup ? 'active' : '' }}"
                href="#" data-toggle="collapse"
                aria-expanded="{{ $isWebsiteSetup ? 'true' : 'false' }}"
                data-target="#website-setup" aria-controls="website-setup">
                <i class="fas fa-globe"></i>Website Setup
            </a>
            <div id="website-setup" class="{{ $isWebsiteSetup ? '' : 'collapse' }} submenu">
                <ul class="nav flex-column">
                    @if (hasPermission('slider_read'))
                    <li class="nav-item">
                        <a class="nav-link {{ $isFrontWebSlider ? 'active' : '' }}"
                            href="{{ route('slider.index') }}">Slider</a>
                    </li>
                    @endif
                    @if (hasPermission('social_link_read') == true)
                    <li class="nav-item">
                        <a class="nav-link {{ $isFrontWebSocialLink ? 'active' : '' }}"
                            href="{{ route('social.link.index') }}">Social Link</a>
                    </li>
                    @endif
                    @if (hasPermission('service_read') == true)
                    <li class="nav-item">
                        <a class="nav-link {{ $isFrontWebService ? 'active' : '' }}"
                            href="{{ route('service.index') }}">Service</a>
                    </li>
                    @endif
                    @if (hasPermission('why_courier_read') == true)
                    <li class="nav-item">
                        <a class="nav-link {{ $isFrontWebWhyCourier ? 'active' : '' }}"
                            href="{{ route('why.courier.index') }}">Why Courier</a>
                    </li>
                    @endif
                    @if (hasPermission('faq_read') == true)
                    <li class="nav-item">
                        <a class="nav-link {{ $isFrontWebFaq ? 'active' : '' }}"
                            href="{{ route('faq.index') }}">FAQ</a>
                    </li>
                    @endif
                    @if (hasPermission('partner_read') == true)
                    <li class="nav-item">
                        <a class="nav-link {{ $isFrontWebPartner ? 'active' : '' }}"
                            href="{{ route('partner.index') }}">Partner</a>
                    </li>
                    @endif
                    @if (hasPermission('pages_read') == true)
                    <li class="nav-item">
                        <a class="nav-link {{ $isFrontWebPages ? 'active' : '' }}"
                            href="{{ route('pages.index') }}">Pages</a>
                    </li>
                    @endif
                    @if (hasPermission('section_read') == true)
                    <li class="nav-item">
                        <a class="nav-link {{ $isFrontWebSection ? 'active' : '' }}"
                            href="{{ route('section.index') }}">Section</a>
                    </li>
                    @endif
                </ul>
            </div>
        </li>
        @endif

        @if (hasPermission('delivery_category_read') == true ||
        hasPermission('delivery_charge_read') == true ||
        hasPermission('delivery_type_read') == true ||
        hasPermission('liquid_fragile_read') == true ||
        hasPermission('packaging_read') == true ||
        hasPermission('country_read') == true ||
        hasPermission('province_read') == true ||
        hasPermission('city_read') == true ||
        hasPermission('district_read') == true ||
        hasPermission('town_read') == true ||
        hasPermission('user_read') == true ||
        hasPermission('zone_delivery_charge_read') == true ||
        hasPermission('account_read') == true ||
        hasPermission('fund_transfer_read') == true ||
        hasPermission('cash_received_from_delivery_man_read') == true ||

        hasPermission('parcel_category_read') == true)

        <!---for setting--->
        <li class="nav-item">
            <a class="nav-link {{ $isSettingsMenu ? 'active' : '' }} "
                href="#" data-toggle="collapse" data-target="#submenu-0" aria-controls="submenu-0"><i
                    class="fa fa-cogs"></i>
                {{ __('menus.settings') }}</a>
            <div class="{{ $isSettingsMenu ? '' : 'collapse' }} submenu"
                id="submenu-0" class="collapse submenu">
                <ul class="nav flex-column">


                    @if (hasPermission('general_settings_read') == true)
                    <li class="nav-item">
                        <a class="nav-link {{ $isGeneralSettings ? 'active' : '' }}"
                            href="{{ route('general-settings.index') }}">{{ __('menus.general_settings') }}</a>
                    </li>
                    @endif
                    @if (hasPermission('general_settings_read') == true)
                    <li class="nav-item">
                        <a class="nav-link {{ $isMailSettings ? 'active' : '' }}"
                            href="{{ route('mail-settings.index') }}">Mail Settings</a>
                    </li>
                    @endif
                    <!-- @if (hasPermission('shipping_type_read') == true)
                    <li class="nav-item">
                        <a class="nav-link {{ $isShippingType ? 'active' : '' }}"
                            href="{{ route('shipping-type.index') }}">{{ __('parcel.shipping_type') }}</a>
                    </li>
                    @endif -->
                    <!-- @if (hasPermission('liquid_fragile_read') == true)
                    <li class="nav-item">
                        <a class="nav-link {{ $isLiquidFragile ? 'active' : '' }}"
                            href="{{ route('liquid-fragile.index') }}">{{ __('menus.liquid_fragile') }}</a>
                    </li>
                    @endif -->
                    <!-- @if (hasPermission('extra_cost_read') == true)
                    <li class="nav-item">
                        <a class="nav-link {{ $isExtraCost ? 'active' : '' }}"
                            href="{{ route('extra_cost.index') }}">{{ __('menus.extra_cost') }}</a>
                    </li>
                    @endif -->

                    @if (hasPermission('sms_settings_read') == true)
                    <li class="nav-item">
                        <a class="nav-link {{ $isSmsSettings ? 'active' : '' }}"
                            href="{{ route('sms-settings.index') }}">{{ __('menus.sms_settings') }}</a>
                    </li>
                    @endif
                    @if (hasPermission('sms_send_settings_read') == true)
                    <li class="nav-item">
                        <a class="nav-link {{ $isSmsSendSettings ? 'active' : '' }}"
                            href="{{ route('sms-send-settings.index') }}">{{ __('menus.sms_send_settings') }}</a>
                    </li>
                    @endif
                    @if (hasPermission('province_read') == true)
                    <li class="nav-item">
                        <a class="nav-link {{ $isProvince ? 'active' : '' }}"
                            href="{{ route('province.index') }}">{{ __('levels.province') }}</a>
                    </li>
                    @endif
                    @if (hasPermission('city_read') == true)
                    <li class="nav-item">
                        <a class="nav-link {{ $isCities ? 'active' : '' }}"
                            href="{{ route('cities.index') }}">{{ __('city.title') }}</a>
                    </li>
                    @endif

                    <!-- @if (hasPermission('notification_settings_read') == true)
                    <li class="nav-item">
                        <a class="nav-link {{ $isNotificationSettings ? 'active' : '' }}"
                            href="{{ route('notification-settings.index') }}">{{ __('menus.notification_settings') }}</a>
                    </li>
                    @endif -->

                    @if (hasPermission('payout_setup_settings_read') == true)
                    <li class="nav-item">
                        <a class="nav-link {{ $isPayoutSetupSettings ? 'active' : '' }}"
                            href="{{ route('payout.setup.settings.index') }}">{{ __('menus.payout_setup') }}</a>
                    </li>
                    @endif
                    <!-- @if (hasPermission('packaging_read') == true)
                    <li class="nav-item">
                        <a class="nav-link {{ $isPackaging ? 'active' : '' }}"
                            href="{{ route('packaging.index') }}">{{ __('menus.packaging') }}</a>
                    </li>
                    @endif -->
                    <!-- @if (hasPermission('currency_read') == true)
                    <li class="nav-item">
                        <a class="nav-link {{ $isCurrency ? 'active' : '' }}"
                            href="{{ route('currency.index') }}">{{ __('settings.currency') }}</a>
                    </li>
                    @endif -->

                    <!-- @if (hasPermission('parcel_category_read') == true)
                    <li class="nav-item">
                        <a class="nav-link  {{ $isParcelCategory ? 'active' : '' }} "
                            href="{{ route('parcel.category.index') }}">{{ __('parcelcategory.parcel_category') }}</a>
                    </li>
                    @endif -->

                    <!-- @if (hasPermission('invoice_generate_menually') == true)
                    <li class="nav-item">
                        <a class="nav-link {{ $isInvoiceGenerateMenually ? 'active' : '' }}"
                            href="{{ route('invoice.generate.menually.index') }}">{{ __('menus.invoice_generate_menually') }}</a>
                    </li>
                    @endif -->
                </ul>
            </div>
        </li>
        @endif
    </ul>
</div>

<!-- end left sidebar -->