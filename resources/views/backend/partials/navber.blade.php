<!-- navbar -->
<nav class="navbar navbar-expand-lg center-nav transparent navbar-light p-3 fixed-top">
    <div class="d-flex w-100 justify-content-between flex-lg-row flex-nowrap align-items-center">
        <div class="navbar-collapse offcanvas offcanvas-nav offcanvas-start text-bg-dark " tabindex="-1"
            id="offcanvasDarkNavbar" aria-labelledby="offcanvasDarkNavbarLabel">
            <div class="offcanvas-header w-90 ">
                <h3 class="text-white fs-30 mb-0">{{ settings()->name }}</h3>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"
                    aria-label="Close">
                </button>
            </div>
            <div class="offcanvas-body ms-lg-auto d-flex flex-column h-100 w-90">
                <div class="dashboard-header">
                    <nav class="navbar navbar-expand-lg navbar-light  fixed-top   ">
                        <a class="navbar-brand w-100 mr-0" href="{{ route('dashboard.index') }}" style="max-width: 264px; background: var(--bs-dark);">
                            <img src="{{ settings()->logo_image }}" class="logo" />
                        </a>
                        <div class="dropdown lang-dropdown navbar_menus changeLocale mobileLocale ">
                            <button class="btn  dropdown-toggle" type="button" id="dropdownMenuButton"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                @if (app()->getLocale() == 'en')
                                <i class="flag-icon flag-icon-us"></i> {{ __('levels.english') }}
                                @elseif(app()->getLocale() == 'ar')
                                <i class="flag-icon flag-icon-sa"></i> {{ __('levels.arabic') }}
                                @elseif(app()->getLocale() == 'fr')
                                <i class="flag-icon flag-icon-fr"></i> {{ __('levels.franch') }}
                                @endif
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <a class="dropdown-item" href="{{ route('setlocalization', 'en') }}">
                                    <i class="flag-icon flag-icon-us"></i> {{ __('levels.english') }}
                                </a>
                                <a class="dropdown-item" href="{{ route('setlocalization', 'ar') }}">
                                    <i class="flag-icon flag-icon-sa"></i> {{ __('levels.arabic') }}
                                </a>
                                <a class="dropdown-item" href="{{ route('setlocalization', 'fr') }}">
                                    <i class="flag-icon flag-icon-fr"></i> {{ __('levels.franch') }}
                                </a>
                            </div>
                        </div>
                        <div class=" navbar-collapse" id="navbarSupportedContent">
                            <ul class="navbar-nav ml-3 navbar-left-top">
                                @if (isset($data['dashboard']) && $data['dashboard'])
                                <li class="nav-item d-none d-lg-block" style="padding:0 10px">
                                    <form action="{{ route('dashboard.index') }}" method="get">
                                        <input type="hidden" name="test" value="custom" />
                                        <select class="form-control select2" name="province_id" id="province_id">
                                            <option value="" selected>{{ __('levels.select') }} {{ __('parcel.province') }}</option>
                                            @foreach (\App\Models\Backend\Province::all() as $province)
                                            <option value="{{ $province->id }}" @selected(old('province_id', $request->province_id) == $province->id)>
                                                {{ @$province->name }}({{ $province->province_code }})
                                            </option>
                                            @endforeach
                                        </select>
                                        <select class="form-control select2 me-2 d-none d-xl-block" name="merchant_id" id="merchant_id">
                                            <option value="" value="">{{ __('levels.select') }} {{ __('parcel.merchant') }}</option>
                                            @foreach (\App\Models\Backend\Merchant::all() as $merchant)
                                            <option value="{{ $merchant->id }}" @selected(old('merchant_id', $request->merchant_id) == $merchant->id)>
                                                {{ $merchant->user->name }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </form>
                                </li>
                                @endif
                            </ul>
                            <ul class="navbar-nav ml-auto navbar-right-top">
                                <li class="nav-item" style="padding:15px">
                                    <a href="#" class="text-dark font-weight-bold" style="font-size: 15px; @media (max-width: 991px) { color: #fff !important; }">
                                        <span class="clock"></span>
                                    </a>
                                    <a href="#" class="ms-3 d-none" data-bs-toggle="modal" data-bs-target="#attendance-modal" data-title="{{ __('parcel.check_in') }}">
                                        <i class="fa fa-sign-in-alt" style="font-size: 15px"></i>
                                    </a>
                                </li>
                                <li class="nav-item lang">
                                    <div class="form-group col-12 pt-1 mb-0">
                                        <div class="dropdown lang-dropdown">
                                            <button class="btn  dropdown-toggle" type="button" id="dropdownMenuButton"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                @if (app()->getLocale() == 'en')
                                                <i class="flag-icon flag-icon-us"></i> {{ __('levels.english') }}
                                                @elseif(app()->getLocale() == 'ar')
                                                <i class="flag-icon flag-icon-sa"></i> {{ __('levels.arabic') }}
                                                @elseif(app()->getLocale() == 'fr')
                                                <i class="flag-icon flag-icon-fr"></i> {{ __('levels.franch') }}
                                                @endif
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                <a class="dropdown-item" href="{{ route('setlocalization', 'en') }}">
                                                    <i class="flag-icon flag-icon-us"></i> {{ __('levels.english') }}
                                                </a>
                                                <a class="dropdown-item" href="{{ route('setlocalization', 'ar') }}">
                                                    <i class="flag-icon flag-icon-sa"></i> {{ __('levels.arabic') }}
                                                </a>
                                                <a class="dropdown-item" href="{{ route('setlocalization', 'fr') }}">
                                                    <i class="flag-icon flag-icon-fr"></i> {{ __('levels.franch') }}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <li class="nav-item dropdown nav-user navbar_menus">
                                    @if (hasPermission('dashboard_read') == true)
                                    <a class="dropdown-item {{ request()->is('/*') ? 'active' : '' }}" href="{{ url('/') }}">
                                        <i class="fa fa-home"></i> {{ __('menus.dashboard') }}
                                    </a>
                                    @endif
                                </li>

                                <li class="nav-item dropdown nav-user navbar_menus">
                                    <a class="dropdown-item {{ request()->is('admin/order*', 'admin/parcel/specific/search*', 'admin/customer*', 'admin/dispatcher*', 'admin/driver*', 'admin/return/report*', 'admin/statistics*') ? 'active' : '' }}"
                                        href="#"
                                        data-toggle="collapse"
                                        aria-expanded="{{ request()->is('admin/order*', 'admin/parcel/specific/search*', 'admin/customer*', 'admin/dispatcher*', 'admin/driver*', 'admin/return/report*', 'admin/statistics*') ? 'true' : 'false' }}"
                                        data-target="#order-management1"
                                        aria-controls="order-management1">
                                        <div class="d-flex justify-content-between text-white">
                                            <span> <i class="fas fa-shop"></i>
                                                {{ __('menus.order_management') }}</span>
                                            <span><i class="fa fa-angle-down"></i></span>
                                        </div>
                                    </a>
                                    <div id="order-management1" class="{{ request()->is('admin/order*', 'admin/parcel/specific/search*', 'admin/customer*', 'admin/dispatcher*', 'admin/driver*', 'admin/return/report*', 'admin/statistics*') ? '' : 'collapse' }} submenu">
                                        <ul class="nav flex-column nav-user-dropdown">
                                            @if (hasPermission('parcel_create') == true)
                                            <a class="dropdown-item {{ request()->is('admin/order/create') ? 'active' : '' }}" href="{{ route('parcel.create') }}">
                                                {{ __('menus.create_order') }}
                                            </a>
                                            @endif
                                            @if (hasPermission('customer_read') == true)
                                            <a class="dropdown-item {{ request()->is('admin/customer*') ? 'active' : '' }}" href="{{ route('customer.index') }}">
                                                {{ __('menus.sender') }}
                                            </a>
                                            @endif
                                            @if (hasPermission('delivery_man_read') == true)
                                            <a class="dropdown-item {{ request()->is('admin/driver*') ? 'active' : '' }}" href="{{ route('deliveryman.index') }}">
                                                {{ __('menus.deliveryman') }}
                                            </a>
                                            @endif
                                            @if (hasPermission('parcel_read') == true)
                                            <a class="dropdown-item {{ request()->is('admin/order*') || request()->is('admin/parcel/specific/search*') ? 'active' : '' }}" href="{{ route('parcel.index') }}">
                                                {{ __('menus.order_monitoring') }}
                                            </a>
                                            @endif
                                            @if (hasPermission('return_report_read') == true)
                                            <a class="dropdown-item {{ request()->is('admin/return/report*') ? 'active' : '' }}" href="{{ route('parcel.return_report') }}">
                                                {{ __('menus.return_report') }}
                                            </a>
                                            @endif
                                            @if (hasPermission('statistics_read'))
                                            <a class="dropdown-item {{ request()->is('admin/statistics*') ? 'active' : '' }}" href="{{ route('admin.statistics') }}">
                                                {{ __('menus.order_history_and_analytics') }}
                                            </a>
                                            @endif
                                        </ul>
                                    </div>
                                </li>
                                <li class="nav-item dropdown nav-user navbar_menus">
                                    <a class="dropdown-item {{ request()->is('admin/request/dispatcher/payment*', 'admin/dispatcher/incharge*', 'admin/dispatcher/view*', 'admin/request/dispatcher/payment*', 'admin/payment*', 'admin/pickup-request*', 'admin/assets*', 'admin/vehicles*') ? 'active' : '' }}"
                                        href="#" data-toggle="collapse"
                                        aria-expanded="{{ request()->is('admin/request/dispatcher/payment*', 'admin/dispatcher/incharge*', 'admin/dispatcher/view*', 'admin/request/dispatcher/payment*', 'admin/payment*', 'admin/pickup-request*', 'admin/assets*', 'admin/vehicles*') ? 'true' : 'false' }}"
                                        data-target="#courier-management1"
                                        aria-controls="courier-management1">
                                        <div class="d-flex justify-content-between text-white">
                                            <span> <i class="fas fa-shop"></i>
                                                {{ __('menus.courier_management') }}</span>
                                            <span><i class="fa fa-angle-down"></i></span>
                                        </div>
                                    </a>
                                    <div id="courier-management1"
                                        class="{{ request()->is('admin/request/dispatcher/payment*', 'admin/dispatcher/incharge*', 'admin/dispatcher/view*', 'admin/request/dispatcher/payment*', 'admin/payment*', 'admin/pickup-request*', 'admin/assets*', 'admin/vehicles*') ? '' : 'collapse' }} submenu">
                                        <ul class="nav flex-column nav-user-dropdown">
                                            @if (hasPermission('payment_read') == true)
                                            <a class="dropdown-item {{ request()->is('admin/payment*') ? 'active' : '' }}" href="{{ route('merchant.manage.payment.index') }}">
                                                {{ __('menus.sender_payments') }}
                                            </a>
                                            @endif
                                            @if (hasPermission('pickup_request_regular') == true || hasPermission('pickup_request_express') == true)
                                            <li class="nav-item">
                                                <a class="dropdown-item {{ request()->is('admin/pickup-request*') ? 'active' : '' }}"
                                                    href="#"
                                                    data-toggle="collapse"
                                                    aria-expanded="{{ request()->is('admin/pickup-request*') ? 'true' : 'false' }}"
                                                    data-target="#pickup-requested1"
                                                    aria-controls="pickup-requested1"
                                                    style="padding-right:10px!important">
                                                    <div class="d-flex justify-content-between text-white">
                                                        <span> {{ __('menus.pickup_request') }}</span>
                                                        <span><i class="fa fa-angle-down"></i></span>
                                                    </div>
                                                </a>
                                                <div id="pickup-requested1" class="{{ request()->is('admin/pickup-request*') ? '' : 'collapse' }} submenu">
                                                    <ul class="nav flex-column">
                                                        @if (hasPermission('pickup_request_regular') == true)
                                                        <a class="dropdown-item {{ request()->is('admin/pickup-request/regular*') ? 'active' : '' }}" href="{{ route('pickup.request.regular') }}">
                                                            {{ __('menus.regular') }}
                                                        </a>
                                                        @endif
                                                        @if (hasPermission('pickup_request_express') == true)
                                                        <a class="dropdown-item {{ request()->is('admin/pickup-request/express*') ? 'active' : '' }}" href="{{ route('pickup.request.express') }}">
                                                            {{ __('menus.express') }}
                                                        </a>
                                                        @endif
                                                    </ul>
                                                </div>
                                            </li>
                                            @endif
                                            @if (hasPermission('vehicle_read') == true)
                                            <a class="dropdown-item {{ request()->is('admin/vehicles*') ? 'active' : '' }}" href="{{ route('vehicles.index') }}">
                                                {{ __('menus.vehicle') }}
                                            </a>
                                            @endif
                                        </ul>
                                    </div>
                                </li>
                                <li class="nav-item dropdown nav-user navbar_menus">
                                    <a class="dropdown-item {{ request()->is('admin/push-notification*', 'admin/addons*','admin/analytics', 'admin/reports/*', 'admin/attendance/report*', 'admin/leave/report*') ? 'active' : '' }}"
                                        href="#"
                                        data-toggle="collapse"
                                        aria-expanded="{{ request()->is('admin/push-notification*', 'admin/addons*','admin/analytics', 'admin/reports/*', 'admin/attendance/report*', 'admin/leave/report*') ? 'true' : 'false' }}"
                                        data-target="#data-analytics1"
                                        aria-controls="data-analytics1">
                                        <div class="d-flex justify-content-between text-white">
                                            <span> <i class="fa fa-chart-line"></i> {{ __('menus.data_analytics_statastics') }}</span>
                                            <span><i class="fa fa-angle-down"></i></span>
                                        </div>
                                    </a>
                                    <div id="data-analytics1"
                                        class="{{ request()->is('admin/push-notification*', 'admin/addons*','admin/analytics', 'admin/reports/*', 'admin/attendance/report*', 'admin/leave/report*') ? '' : 'collapse' }} submenu">
                                        <ul class="nav flex-column nav-user-dropdown">
                                            {{-- reports --}}
                                            @if (hasPermission('parcel_status_reports') == true ||
                                            hasPermission('attendance_reports') == true ||
                                            hasPermission('leave_reports') == true)
                                            <li class="nav-item">
                                                <a class="dropdown-item {{ request()->is('admin/push-notification*', 'admin/reports/*', 'admin/attendance/report*', 'admin/leave/report*') ? 'active' : '' }}"
                                                    href="#" data-toggle="collapse"
                                                    aria-expanded="{{ request()->is('admin/push-notification*', 'admin/reports/*', 'admin/attendance/report*', 'admin/leave/report*') ? 'true' : 'false' }}"
                                                    data-target="#reports1" aria-controls="reports1" style="padding-right: 10px!important">

                                                    <div class="d-flex justify-content-between text-white">
                                                        <span> {{ __('reports.title') }}</span>
                                                        <span><i class="fa fa-angle-down"></i></span>
                                                    </div>
                                                </a>
                                                <div id="reports1"
                                                    class="{{ request()->is('admin/push-notification*', 'admin/reports/*', 'admin/attendance/report*', 'admin/leave/report*') ? '' : 'collapse' }} submenu">
                                                    <ul class="nav flex-column">
                                                        @if (hasPermission('parcel_status_reports') == true)
                                                        <li class="nav-item ">
                                                            <a class="nav-link {{ request()->is('admin/reports/parcel-reports*', 'admin/reports/parcel-filter-reports') ? 'active' : '' }}"
                                                                href="{{ route('parcel.reports') }}" aria-expanded="false"
                                                                data-target="#submenu-1"
                                                                aria-controls="submenu-1">{{ __('reports.parcel_reports') }}</a>
                                                        </li>
                                                        <li class="nav-item">
                                                            <a class="nav-link {{ request()->is('admin/reports/rider-overview*') ? 'active' : '' }}"
                                                                href="{{ route('rider.overview') }}">Rider Overview</a>
                                                        </li>
                                                        @endif
                                                        @if (hasPermission('parcel_status_reports') == true)
                                                        <li class="nav-item">
                                                            <a class="nav-link {{ request()->is('admin/reports/marketplace-earnings*') ? 'active' : '' }}"
                                                                href="{{ route('marketplace.earnings') }}">Marketplace Earnings</a>
                                                        </li>
                                                        @endif
                                                    </ul>
                                                </div>
                                            </li>
                                            @endif
                                            {{-- end reports --}}
                                            @if (hasPermission('analytics_read'))
                                            <li class="nav-item ">
                                                <a class="nav-link {{ request()->is('admin/analytics*') ? 'active' : '' }}"
                                                    href="{{ route('admin.analytics') }}">
                                                    {{ __('parcel.analytics') }}</a>
                                            </li>
                                            @endif
                                            @if (hasPermission('delivery_charge_read') == true)
                                            <li class="nav-item ">
                                                <a class="nav-link {{ request()->is('admin/delivery-charge*') ? 'active' : '' }}"
                                                    href="{{ route('delivery-charge.index') }}">
                                                    Delivery Charges</a>
                                            </li>
                                            @endif
                                            @if (hasPermission('push_notification_read') == true)
                                            <li class="nav-item ">
                                                <a class="nav-link {{ request()->is('admin/push-notification*') ? 'active' : '' }}"
                                                    href="{{ route('push-notification.index') }}" aria-expanded="false"
                                                    data-target="#submenu-1" aria-controls="submenu-1">
                                                    {{ __('menus.push_notification') }}</a>
                                            </li>
                                            @endif
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
                                hasPermission('payout_read') == true)
                                <li class="nav-item dropdown nav-user navbar_menus">
                                    <a class="dropdown-item {{ request()->is('admin/accounts*', 'admin/fund-transfer*', 'admin/account-head*', 'admin/bank-transaction*', 'admin/paid/invoice*', 'admin/salary/salary-generate*', 'admin/salary*') ? 'active' : '' }} "
                                        href="#"
                                        data-toggle="collapse"
                                        aria-expanded="{{ request()->is('admin/accounts*', 'admin/fund-transfer*', 'admin/expense*', 'admin/income*', 'admin/account-head*', 'admin/bank-transaction*', 'admin/paid/invoice*', 'admin/online-payment-list*', 'admin/payout*', 'admin/salary/salary-generate*', 'admin/salary*') ? 'true' : 'false' }}"
                                        data-target="#account1"
                                        aria-controls="account1">
                                        <div class="d-flex justify-content-between text-white">
                                            <span> <i class="fas fa-user"></i> {{ __('menus.finance_and_accounting') }}</span>
                                            <span><i class="fa fa-angle-down"></i></span>
                                        </div>
                                    </a>
                                    <div id="account1" class="{{ request()->is('admin/accounts*', 'admin/fund-transfer*', 'admin/expense*', 'admin/income*', 'admin/account-head*', 'admin/bank-transaction*', 'admin/paid/invoice*', 'admin/online-payment-list*', 'admin/payout*', 'admin/salary/salary-generate*', 'admin/salary*') ? '' : 'collapse' }} submenu">
                                        <ul class="nav flex-column nav-user-dropdown">
                                            @if (hasPermission('salary_generate_read') == true || hasPermission('salary_read') == true)
                                            <li class="nav-item">
                                                <a class="dropdown-item {{ request()->is('admin/salary/salary-generate*', 'admin/salary*') ? 'active' : '' }}"
                                                    href="#" data-toggle="collapse"
                                                    aria-expanded="{{ request()->is('admin/salary/salary-generate*', 'admin/salary*') ? 'true' : 'false' }}"
                                                    data-target="#salarygenerate1"
                                                    aria-controls="salarygenerate1" style="padding-right: 10px!important">

                                                    <div class="d-flex justify-content-between text-white">
                                                        <span> {{ __('salary.payroll') }}</span>
                                                        <span><i class="fa fa-angle-down"></i></span>
                                                    </div>
                                                </a>
                                                <div id="salarygenerate1"
                                                    class="{{ request()->is('admin/salary/salary-generate*', 'admin/salary*') ? '' : 'collapse' }} submenu">
                                                    <ul class="nav flex-column">
                                                        @if (hasPermission('salary_generate_read') == true)
                                                        <li class="nav-item ">
                                                            <a class="dropdown-item {{ request()->is('admin/salary/salary-generate*', 'admin/reports/parcel-filter-reports') ? 'active' : '' }}" href="{{ route('salary.generate.index') }}">
                                                                {{ __('salary.salary_generate') }}
                                                            </a>
                                                        </li>
                                                        @endif
                                                        @if (hasPermission('salary_read') == true)
                                                        <li class="nav-item">
                                                            <a class="dropdown-item {{ request()->is('admin/salarys*') ? 'active' : '' }}" href="{{ route('salary.index') }}">
                                                                {{ __('menus.salary') }}
                                                            </a>
                                                        </li>
                                                        @endif
                                                    </ul>
                                                </div>
                                            </li>
                                            @endif
                                            @if (hasPermission('account_heads_read') == true)
                                            <li class="nav-item">
                                                <a class="dropdown-item {{ request()->is('admin/account-heads*') ? 'active' : '' }}" href="{{ route('account.heads.index') }}">
                                                    {{ __('menus.account_heads') }}
                                                </a>
                                            </li>
                                            @endif
                                            @if (hasPermission('account_read') == true)
                                            <li class="nav-item">
                                                <a class="dropdown-item {{ request()->is('admin/accounts*') ? 'active' : '' }}" href="{{ route('accounts.index') }}">
                                                    {{ __('menus.accounts') }}
                                                </a>
                                            </li>
                                            @endif
                                            @if (hasPermission('fund_transfer_read') == true)
                                            <li class="nav-item">
                                                <a class="dropdown-item {{ request()->is('admin/fund-transfer*') ? 'active' : '' }}"
                                                    href="{{ route('fund-transfer.index') }}">{{ __('menus.fund_transfer') }}</a>
                                            </li>
                                            @endif
                                            @if (hasPermission('income_read') == true)
                                            <li class="nav-item">
                                                <a class="dropdown-item {{ request()->is('admin/income*') ? 'active' : '' }}"
                                                    href="{{ route('income.index') }}">{{ __('menus.income') }}</a>
                                            </li>
                                            @endif
                                            @if (hasPermission('expense_read') == true)
                                            <li class="nav-item">
                                                <a class="dropdown-item {{ request()->is('admin/expense*') ? 'active' : '' }}"
                                                    href="{{ route('expense.index') }}">{{ __('menus.expense') }}</a>
                                            </li>
                                            @endif

                                            @if (hasPermission('bank_transaction_read') == true)
                                            <li class="nav-item">
                                                <a class="dropdown-item {{ request()->is('admin/bank-transaction*') ? 'active' : '' }}"
                                                    href="{{ route('bank-transaction.index') }}">{{ __('menus.bank_transaction') }}</a>
                                            </li>
                                            @endif

                                            @if (hasPermission('paid_invoice_read') == true)
                                            <li class="nav-item">
                                                <a class="dropdown-item {{ request()->is('admin/paid/invoice*') ? 'active' : '' }}"
                                                    href="{{ route('paid.invoice.index') }}">{{ __('invoice.paid_invoice') }}</a>
                                            </li>
                                            @endif


                                            @if (hasPermission('online_payment_read') == true)
                                            <li class="nav-item ">
                                                <a class="dropdown-item {{ request()->is('admin/online-payment-list*') ? 'active' : '' }}"
                                                    href="{{ route('online.payment.list') }}">

                                                    {{ __('menus.payments_received') }}</a>
                                            </li>
                                            @endif

                                            @if (hasPermission('payout_read') == true)
                                            <li class="nav-item ">
                                                <a class="dropdown-item {{ request()->is('admin/payout*') ? 'active' : '' }}"
                                                    href="{{ route('payout.index') }}">

                                                    {{ __('menus.payout') }}</a>
                                            </li>
                                            @endif
                                            <li class="nav-item ">
                                                <a class="dropdown-item {{ request()->is('admin/rider-withdraw-requests*') ? 'active' : '' }}"
                                                    href="{{ route('rider.withdraw.requests.index') }}">
                                                    Rider Withdraw Requests</a>
                                            </li>


                                        </ul>
                                    </div>
                                    </a>
                                    <div id="account1" class="{{ request()->is('admin/accounts*', 'admin/fund-transfer*', 'admin/expense*', 'admin/income*', 'admin/account-head*', 'admin/bank-transaction*', 'admin/paid/invoice*', 'admin/online-payment-list*', 'admin/payout*', 'admin/salary/salary-generate*', 'admin/salary*') ? '' : 'collapse' }} submenu">
                                        <ul class="nav flex-column nav-user-dropdown">
                                            @if (hasPermission('salary_generate_read') == true || hasPermission('salary_read') == true)
                                            <li class="nav-item">
                                                <a class="dropdown-item {{ request()->is('admin/salary/salary-generate*', 'admin/salary*') ? 'active' : '' }}"
                                                    href="#" data-toggle="collapse"
                                                    aria-expanded="{{ request()->is('admin/salary/salary-generate*', 'admin/salary*') ? 'true' : 'false' }}"
                                                    data-target="#salarygenerate1"
                                                    aria-controls="salarygenerate1" style="padding-right: 10px!important">

                                                    <div class="d-flex justify-content-between text-white">
                                                        <span> {{ __('salary.payroll') }}</span>
                                                        <span><i class="fa fa-angle-down"></i></span>
                                                    </div>
                                                </a>
                                                <div id="salarygenerate1"
                                                    class="{{ request()->is('admin/salary/salary-generate*', 'admin/salary*') ? '' : 'collapse' }} submenu">
                                                    <ul class="nav flex-column">
                                                        @if (hasPermission('salary_generate_read') == true)
                                                        <li class="nav-item ">
                                                            <a class="dropdown-item {{ request()->is('admin/salary/salary-generate*', 'admin/reports/parcel-filter-reports') ? 'active' : '' }}" href="{{ route('salary.generate.index') }}">
                                                                {{ __('salary.salary_generate') }}
                                                            </a>
                                                        </li>
                                                        @endif
                                                        @if (hasPermission('salary_read') == true)
                                                        <li class="nav-item">
                                                            <a class="dropdown-item {{ request()->is('admin/salarys*') ? 'active' : '' }}" href="{{ route('salary.index') }}">
                                                                {{ __('menus.salary') }}
                                                            </a>
                                                        </li>
                                                        @endif
                                                    </ul>
                                                </div>
                                            </li>
                                            @endif
                                            @if (hasPermission('account_heads_read') == true)
                                            <li class="nav-item">
                                                <a class="dropdown-item {{ request()->is('admin/account-heads*') ? 'active' : '' }}" href="{{ route('account.heads.index') }}">
                                                    {{ __('menus.account_heads') }}
                                                </a>
                                            </li>
                                            @endif
                                            @if (hasPermission('account_read') == true)
                                            <li class="nav-item">
                                                <a class="dropdown-item {{ request()->is('admin/accounts*') ? 'active' : '' }}" href="{{ route('accounts.index') }}">
                                                    {{ __('menus.accounts') }}
                                                </a>
                                            </li>
                                            @endif
                                            @if (hasPermission('fund_transfer_read') == true)
                                            <li class="nav-item">
                                                <a class="dropdown-item {{ request()->is('admin/fund-transfer*') ? 'active' : '' }}"
                                                    href="{{ route('fund-transfer.index') }}">{{ __('menus.fund_transfer') }}</a>
                                            </li>
                                            @endif
                                            @if (hasPermission('income_read') == true)
                                            <li class="nav-item">
                                                <a class="dropdown-item {{ request()->is('admin/income*') ? 'active' : '' }}"
                                                    href="{{ route('income.index') }}">{{ __('menus.income') }}</a>
                                            </li>
                                            @endif
                                            @if (hasPermission('expense_read') == true)
                                            <li class="nav-item">
                                                <a class="dropdown-item {{ request()->is('admin/expense*') ? 'active' : '' }}"
                                                    href="{{ route('expense.index') }}">{{ __('menus.expense') }}</a>
                                            </li>
                                            @endif

                                            @if (hasPermission('bank_transaction_read') == true)
                                            <li class="nav-item">
                                                <a class="dropdown-item {{ request()->is('admin/bank-transaction*') ? 'active' : '' }}"
                                                    href="{{ route('bank-transaction.index') }}">{{ __('menus.bank_transaction') }}</a>
                                            </li>
                                            @endif

                                            @if (hasPermission('paid_invoice_read') == true)
                                            <li class="nav-item">
                                                <a class="dropdown-item {{ request()->is('admin/paid/invoice*') ? 'active' : '' }}"
                                                    href="{{ route('paid.invoice.index') }}">{{ __('invoice.paid_invoice') }}</a>
                                            </li>
                                            @endif


                                            @if (hasPermission('online_payment_read') == true)
                                            <li class="nav-item ">
                                                <a class="dropdown-item {{ request()->is('admin/online-payment-list*') ? 'active' : '' }}"
                                                    href="{{ route('online.payment.list') }}">

                                                    {{ __('menus.payments_received') }}</a>
                                            </li>
                                            @endif

                                            @if (hasPermission('payout_read') == true)
                                            <li class="nav-item ">
                                                <a class="dropdown-item {{ request()->is('admin/payout*') ? 'active' : '' }}"
                                                    href="{{ route('payout.index') }}">

                                                    {{ __('menus.payout') }}</a>
                                            </li>
                                            @endif


                                        </ul>
                                    </div>
                                </li>
                                @endif


                                @if (hasPermission('support_read') == true || hasPermission('fraud_read') == true)
                                <li class="nav-item dropdown nav-user navbar_menus">
                                    <a class="dropdown-item {{ request()->is('admin/support*', 'admin/deception*') ? 'active' : '' }}"
                                        href="#" data-toggle="collapse" aria-expanded="false"
                                        data-target="#pickup-request1" aria-controls="hub-manage">
                                        <div class="d-flex justify-content-between text-white">
                                            <span> <i class="fa-solid fa-headset"></i> {{ __('menus.customer_support') }}</span>
                                            <span><i class="fa fa-angle-down"></i></span>
                                        </div>
                                    </a>
                                    <div id="pickup-request1"
                                        class="{{ request()->is('admin/support*', 'admin/deception*') ? '' : 'collapse' }} submenu">
                                        <ul class="nav flex-column nav-user-dropdown">
                                            @if (hasPermission('support_read') == true)
                                            <li class="nav-item ">
                                                <a class="dropdown-item {{ request()->is('admin/support*') ? 'active' : '' }}"
                                                    href="{{ route('support.index') }}"
                                                    aria-expanded="false" data-target="#hubs"
                                                    aria-controls="hubs">{{ __('menus.support') }}</a>
                                            </li>
                                            @endif



                                        </ul>
                                    </div>
                                </li>
                                @endif



                                @if (hasPermission('leave_type_read') == true ||
                                hasPermission('leave_assign_read') == true ||
                                hasPermission('leave_read') == true ||
                                hasPermission('weekend_read') == true ||
                                hasPermission('duty_schedule_read') == true ||
                                hasPermission('attendance_read') == true)
                                <li class="nav-item dropdown nav-user navbar_menus">
                                    <a class="dropdown-item {{ request()->is('admin/hrm*') ? 'active' : '' }}"
                                        href="#" data-toggle="collapse" aria-expanded="false"
                                        data-target="#hrm-manage1" aria-controls="hrm-manage1">
                                        <div class="d-flex justify-content-between text-white">
                                            <span><i class="fa fa-users"></i> {{ __('parcel.hrm') }}</span>
                                            <span><i class="fa fa-angle-down"></i></span>
                                        </div>
                                    </a>
                                </li>
                                @endif


                                @if (hasPermission('subscribe_read') == true || hasPermission('news_offer_read') == true)
                                <li class="nav-item dropdown nav-user navbar_menus">
                                    <a class="dropdown-item {{ request()->is('admin/subscribe*', 'admin/news-offer*') ? 'active' : '' }}"
                                        href="#" data-toggle="collapse" aria-expanded="false"
                                        data-target="#pickup-requests1" aria-controls="pickup-requests1">
                                        <div class="d-flex justify-content-between text-white">
                                            <span><i class="fa-solid fa-handshake"></i>
                                                {{ __('menus.marketing') }}</span>
                                            <span><i class="fa fa-angle-down"></i></span>
                                        </div>
                                    </a>

                                </li>
                                @endif



                                @if (hasPermission('delivery_category_read') == true ||
                                hasPermission('delivery_charge_read') == true ||
                                hasPermission('delivery_type_read') == true ||
                                hasPermission('liquid_fragile_read') == true ||
                                hasPermission('packaging_read') == true ||
                                hasPermission('role_read') == true ||
                                hasPermission('designation_read') == true ||
                                hasPermission('department_read') == true ||
                                hasPermission('country_read') == true ||
                                hasPermission('city_read') == true ||
                                hasPermission('district_read') == true ||
                                hasPermission('town_read') == true ||
                                hasPermission('user_read') == true ||
                                hasPermission('zone_delivery_charge_read') == true ||
                                hasPermission('account_read') == true ||
                                hasPermission('fund_transfer_read') == true ||
                                hasPermission('cash_received_from_delivery_man_read') == true ||
                                hasPermission('role_read') == true ||
                                hasPermission('designation_read') == true ||
                                hasPermission('department_read') == true ||
                                hasPermission('user_read') == true)

                                <!---for setting--->
                                <li class="nav-item dropdown nav-user navbar_menus">
                                    <a class="dropdown-item {{ request()->is('admin/front-web*', 'admin/shipping-type*', 'admin/roles*', 'admin/users*', 'admin/designations*', 'admin/departments*', 'admin/database-backup*', 'admin/delivery-category*', 'admin/delivery-category*', 'admin/delivery-charge*', 'admin/packaging*', 'admin/delivery-type*', 'admin/liquid-fragile*', 'admin/sms-settings*', 'admin/sms-send-settings*', 'admin/general-settings*', 'admin/notification-settings*', 'admin/asset-category*', 'admin/social-login-setting*', 'admin/pay-out/setup*', 'admin/settings/pay-out/setup*', 'admin/settings/invoice-generate-menually*', 'admin/currency*', 'admin/countries*', 'admin/cities*', 'admin/districts*', 'admin/towns*', 'admin/zone-delivery-charge*', 'admin/roles*', 'admin/users*', 'admin/designations*', 'admin/departments*') ? 'active' : '' }} "
                                        href="#" data-toggle="collapse"
                                        aria-expanded="{{ request()->is('admin/front-web*') ? 'true' : 'false' }}"
                                        data-target="#submenu-1" aria-controls="submenu-1">
                                        <div class="d-flex justify-content-between text-white">
                                            <span><i class="fa fa-cogs"></i> {{ __('menus.settings') }}</span>
                                            <span><i class="fa fa-angle-down"></i></span>
                                        </div>
                                    </a>

                                    <div class="{{ request()->is('admin/front-web*', 'admin/shipping-type*', 'admin/roles*', 'admin/users*', 'admin/designations*', 'admin/departments*', 'admin/database-backup*', 'admin/delivery-category*', 'admin/delivery-charge*', 'admin/packaging*', 'admin/delivery-type*', 'admin/liquid-fragile*', 'admin/sms-settings*', 'admin/sms-send-settings*', 'admin/general-settings*', 'admin/notification-settings*', 'admin/asset-category*', 'admin/social-login-setting*', 'admin/pay-out/setup*', 'admin/settings/pay-out/setup*', 'admin/settings/invoice-generate-menually*', 'admin/currency*', 'admin/countries*', 'admin/cities*', 'admin/districts*', 'admin/towns*', 'admin/zone-delivery-charge*', 'admin/roles*', 'admin/users*', 'admin/designations*', 'admin/departments*') ? '' : 'collapse' }} submenu"
                                        id="submenu-1" class="collapse submenu">
                                        <ul class="nav flex-column nav-user-dropdown">

                                            @if (hasPermission('social_link_read') == true || hasPermission('service_read') == true)
                                            <li class="nav-item">
                                                <a class="dropdown-item {{ request()->is('admin/front-web*') ? 'active' : '' }}"
                                                    href="#" data-toggle="collapse"
                                                    aria-expanded="{{ request()->is('admin/front-web*') ? 'true' : 'false' }}"
                                                    data-target="#front-web1" aria-controls="front-web1"
                                                    style="padding-right:10px!important">


                                                    <div class="d-flex justify-content-between text-white">
                                                        <span> {{ __('levels.front_web') }}</span>
                                                        <span><i class="fa fa-angle-down"></i></span>
                                                    </div>

                                                </a>


                                            </li>
                                            @endif
                                            @if (hasPermission('role_read') == true ||
                                            hasPermission('designation_read') == true ||
                                            hasPermission('department_read') == true ||
                                            hasPermission('user_read') == true)
                                            <li class="nav-item">
                                                <a class="dropdown-item {{ request()->is('admin/roles*', 'admin/users*', 'admin/designations*', 'admin/departments*') ? 'active' : '' }} "
                                                    href="#" data-toggle="collapse"
                                                    aria-expanded="{{ request()->is('admin/roles*', 'admin/users*', 'admin/designations*', 'admin/departments*') ? 'true' : 'false' }} "
                                                    data-target="#submenu-21" aria-controls="submenu-21" style="padding-right: 10px!important">

                                                    <div class="d-flex justify-content-between text-white">
                                                        <span> {{ __('menus.user_role') }}</span>
                                                        <span><i class="fa fa-angle-down"></i></span>
                                                    </div>


                                                </a>
                                                <div id="submenu-21"
                                                    class="{{ request()->is('admin/roles*', 'admin/users*', 'admin/designations*', 'admin/departments*') ? '' : 'collapse' }} submenu">
                                                    <ul class="nav flex-column">

                                                        @if (hasPermission('role_read') == true)
                                                        <li class="nav-item">
                                                            <a class="dropdown-item {{ request()->is('admin/roles*') ? 'active' : '' }}"
                                                                href="{{ route('roles.index') }}">{{ __('menus.roles') }}</a>
                                                        </li>
                                                        @endif
                                                        @if (hasPermission('designation_read') == true)
                                                        <li class="nav-item">
                                                            <a class="dropdown-item {{ request()->is('admin/designations*') ? 'active' : '' }}"
                                                                href="{{ route('designations.index') }}">{{ __('menus.designations') }}</a>
                                                        </li>
                                                        @endif
                                                        @if (hasPermission('department_read') == true)
                                                        <li class="nav-item">
                                                            <a class="dropdown-item {{ request()->is('admin/departments*') ? 'active' : '' }}"
                                                                href="{{ route('departments.index') }}">{{ __('menus.departments') }}</a>
                                                        </li>
                                                        @endif

                                                        @if (hasPermission('user_read') == true)
                                                        <li class="nav-item">
                                                            <a class="dropdown-item {{ request()->is('admin/users*') ? 'active' : '' }}"
                                                                href="{{ route('users.index') }}">{{ __('menus.users') }}</a>
                                                        </li>
                                                        @endif

                                                    </ul>
                                                </div>
                                            </li>
                                            @endif
                                            @if (hasPermission('general_settings_read') == true)
                                            <a class="dropdown-item {{ request()->is('admin/general-settings*') ? 'active' : '' }}" href="{{ route('general-settings.index') }}">
                                                {{ __('menus.general_settings') }}
                                            </a>
                                            @endif
                                            @if (hasPermission('shipping_type_read') == true)
                                            <a class="dropdown-item {{ request()->is('admin/shipping-type*') ? 'active' : '' }}" href="{{ route('shipping-type.index') }}">
                                                {{ __('parcel.shipping_type') }}
                                            </a>
                                            @endif
                                            @if (hasPermission('liquid_fragile_read') == true)
                                            <a class="dropdown-item {{ request()->is('admin/liquid-fragile*') ? 'active' : '' }}" href="{{ route('liquid-fragile.index') }}">
                                                {{ __('menus.liquid_fragile') }}
                                            </a>
                                            @endif
                                            @if (hasPermission('extra_cost_read') == true)
                                            <a class="dropdown-item {{ request()->is('admin/extra_cost*') ? 'active' : '' }}" href="{{ route('extra_cost.index') }}">
                                                {{ __('menus.extra_cost') }}
                                            </a>
                                            @endif
                                            @if (hasPermission('sms_settings_read') == true)
                                            <a class="dropdown-item {{ request()->is('admin/sms-settings*') ? 'active' : '' }}" href="{{ route('sms-settings.index') }}">
                                                {{ __('menus.sms_settings') }}
                                            </a>
                                            @endif
                                            @if (hasPermission('sms_send_settings_read') == true)
                                            <a class="dropdown-item {{ request()->is('admin/sms-send-settings*') ? 'active' : '' }}" href="{{ route('sms-send-settings.index') }}">
                                                {{ __('menus.sms_send_settings') }}
                                            </a>
                                            @endif
                                            @if (hasPermission('city_read') == true)
                                            <a class="dropdown-item {{ request()->is('admin/cities*') ? 'active' : '' }}" href="{{ route('cities.index') }}">
                                                {{ __('city.title') }}
                                            </a>
                                            @endif
                                            {{-- @if (hasPermission('district_read') == true)
                                                    <a class="dropdown-item {{ request()->is('admin/districts*') ? 'active' : '' }}" href="{{ route('districts.index') }}">
                                            {{ __('district.title') }}
                                            </a>
                                            @endif
                                            @if (hasPermission('town_read') == true)
                                            <a class="dropdown-item {{ request()->is('admin/towns*') ? 'active' : '' }}" href="{{ route('towns.index') }}">
                                                {{ __('town.title') }}
                                            </a>
                                            @endif --}}
                                            @if (hasPermission('notification_settings_read') == true)
                                            <a class="dropdown-item {{ request()->is('admin/notification-settings*') ? 'active' : '' }}" href="{{ route('notification-settings.index') }}">
                                                {{ __('menus.notification_settings') }}
                                            </a>
                                            @endif

                                            @if (hasPermission('payout_setup_settings_read') == true)
                                            <a class="dropdown-item {{ request()->is('admin/settings/pay-out/setup*') ? 'active' : '' }}" href="{{ route('payout.setup.settings.index') }}">
                                                {{ __('menus.payout_setup') }}
                                            </a>
                                            @endif
                                            @if (hasPermission('packaging_read') == true)
                                            <a class="dropdown-item {{ request()->is('admin/packaging*') ? 'active' : '' }}" href="{{ route('packaging.index') }}">
                                                {{ __('menus.packaging') }}
                                            </a>
                                            @endif
                                            @if (hasPermission('currency_read') == true)
                                            <a class="dropdown-item {{ request()->is('admin/currency*') ? 'active' : '' }}" href="{{ route('currency.index') }}">
                                                {{ __('settings.currency') }}
                                            </a>
                                            @endif

                                            @if (hasPermission('parcel_category_read') == true)
                                            <a class="dropdown-item {{ request()->is('admin/parcel-category*') ? 'active' : '' }} " href="{{ route('parcel.category.index') }}">
                                                {{ __('parcelcategory.parcel_category') }}
                                            </a>
                                            @endif

                                            @if (hasPermission('invoice_generate_menually') == true)
                                            <a class="dropdown-item {{ request()->is('admin/settings/invoice-generate-menually*') ? 'active' : '' }}" href="{{ route('invoice.generate.menually.index') }}">
                                                {{ __('menus.invoice_generate_menually') }}
                                            </a>
                                            @endif
                                        </ul>
                                    </div>
                                </li>
                                @endif


                                <li class="nav-item dropdown admin-panel notification  d-lg-block">
                                    <a href="{{ url('/') }}" class="me-2"><i
                                            class="fa fa-globe navbar-globe"></i></a>
                                </li>

                                <li class="nav-item dropdown admin-panel notification d-lg-block">
                                    <a class="nav-link nav-icons mt-md-3" href="#" id="navbarDropdownMenuLink1"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i
                                            class="fas fa-fw fa-bell"></i> <span class="indicator"></span></a>
                                    <ul class="dropdown-menu dropdown-menu-right notification-dropdown">
                                        <li>
                                            <div class="notification-title"> Notification</div>
                                            <div class="notification-list">
                                                <div class="list-group">
                                                    @foreach (notifications() as $notify)
                                                    <a href="
                                                                @if ($notify['type'] === 'support') {{ route('support.view', $notify['support_id']) }}
                                                                @elseif($notify['type'] === 'newsoffer') {{ route('news-offer.index') }} @endif"
                                                        class="list-group-item list-group-item-action active">
                                                        <div class="notification-info">
                                                            <div class="notification-list-user-img">
                                                                <img src="{{ singleUser($notify['user_id'])->image }}"
                                                                    class="user-avatar-md rounded-circle">
                                                            </div>
                                                            <div class="notification-list-user-block">
                                                                <span class="notification-list-user-name">
                                                                    {{ singleUser($notify['user_id'])->name }}
                                                                </span>
                                                                {{ $notify['subject'] }}
                                                                <div class="notification-date">
                                                                    {{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $notify['created_at'])->diffForHumans() }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </a>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </li>
                                <!---To-do list---->

                                <!---To-do list---->
                                <li class="nav-item dropdown nav-user d-lg-block">
                                    <a class="nav-link nav-user-img" href="#" id="navbarDropdownMenuLink2"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <img src="{{ Auth::user()->image }}" alt="" class="user-avatar-md rounded-circle" style="object-fit: contain">
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right nav-user-dropdown"
                                        aria-labelledby="navbarDropdownMenuLink2">
                                        <div class="nav-user-info">
                                            <h5 class="mb-0 text-white nav-user-name">{{ Auth::user()->name }}</h5>
                                        </div>
                                        <a class="dropdown-item"
                                            href="{{ route('profile.index') }}"><i
                                                class="fas fa-user mr-2"></i>{{ __('menus.profile') }}</a>
                                        <a class="dropdown-item"
                                            href="{{ route('password.change') }}"><i
                                                class="fas fa-key mr-2"></i>{{ __('menus.change_password') }}</a>
                                        <a class="dropdown-item" href="{{ route('logout') }}"
                                            onclick="event.preventDefault();
                                                    document.getElementById('logout-form').submit();">
                                            <i class="fas fa-power-off mr-2"></i>
                                            {{ __('menus.logout') }}
                                        </a>
                                        <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                            class="d-none">
                                            @csrf
                                        </form>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </nav>
                </div>
            </div>
        </div>
        <div class="navbar-other w-100 d-flex justify-content-between ">
            <div class="d-lg-none">
                <a href="{{ route('dashboard.index') }}">
                    <img src="{{ settings()->logo_image }}" style="object-fit: contain; max-width: 200px; height: 40px;"
                        alt="Logo">
                </a>
            </div>
            <ul class="navbar-nav flex-row align-items-center gap-2">
                <li class="nav-item dropdown admin-panel notification  d-lg-none">
                    <a href="{{ url('/') }}" class="me-2"><i class="fa fa-globe"></i></a>
                </li>
                <li class="nav-item dropdown admin-panel notification  d-lg-none">
                    <a class="nav-link nav-icons mt-md-3" href="#" id="navbarDropdownMenuLink1"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i
                            class="fas fa-fw fa-bell"></i> <span
                            class="mobile-notification indicator admin"></span></a>
                    <ul class="dropdown-menu dropdown-menu-right notification-dropdown">
                        <li>
                            <div class="notification-title"> Notification</div>
                            <div class="notification-list">
                                <div class="list-group">
                                    @foreach (notifications() as $notify)
                                    <a href="
                                                @if ($notify['type'] === 'support') {{ route('support.view', $notify['support_id']) }}
                                                @elseif($notify['type'] === 'newsoffer') {{ route('news-offer.index') }} @endif"
                                        class="list-group-item list-group-item-action active">
                                        <div class="notification-info">
                                            <div class="notification-list-user-img">
                                                <img src="{{ singleUser($notify['user_id'])->image }}"
                                                    class="user-avatar-md rounded-circle">
                                            </div>
                                            <div class="notification-list-user-block">
                                                <span class="notification-list-user-name">
                                                    {{ singleUser($notify['user_id'])->name }}
                                                </span>
                                                {{ $notify['subject'] }}
                                                <div class="notification-date">
                                                    {{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $notify['created_at'])->diffForHumans() }}
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                    @endforeach
                                </div>
                            </div>
                        </li>
                    </ul>
                </li>
                <!---To-do list---->

                <!---To-do list---->
                <li class="nav-item dropdown nav-user mobile d-lg-none">
                    <a class="nav-link nav-user-img" href="#" id="navbarDropdownMenuLink2"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <img src="{{ Auth::user()->image }}" alt="" class="user-avatar-md rounded-circle">
                    </a>
                    <div class="dropdown-menu dropdown-menu-right nav-user-dropdown"
                        aria-labelledby="navbarDropdownMenuLink2">
                        <div class="nav-user-info">
                            <h5 class="mb-0 text-white nav-user-name">{{ Auth::user()->name }}</h5>
                        </div>
                        <a class="dropdown-item" href="{{ route('profile.index') }}"><i
                                class="fas fa-user mr-2"></i>{{ __('menus.profile') }}</a>
                        <a class="dropdown-item" href="{{ route('password.change') }}"><i
                                class="fas fa-key mr-2"></i>{{ __('menus.change_password') }}</a>

                        <a class="dropdown-item" href="{{ route('logout') }}"
                            onclick="event.preventDefault();
                                    document.getElementById('logout-form').submit();">
                            <i class="fas fa-power-off mr-2"></i>
                            {{ __('menus.logout') }}
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </div>
                </li>
                <li class="nav-item d-lg-none">
                    <button class="offcanvas-nav-btn" type="button" data-bs-toggle="offcanvas"
                        data-bs-target="#offcanvasDarkNavbar" aria-controls="offcanvasDarkNavbar"><span
                            class="navbar-toggler-icon"></span></button>
                </li>
            </ul>
        </div>
    </div>
</nav>