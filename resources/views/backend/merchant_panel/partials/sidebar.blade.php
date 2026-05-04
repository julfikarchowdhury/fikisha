<!-- left sidebar -->
<div class="col-12 nav-left-sidebar sidebar-dark">
    <ul class="navbar-nav">
        <li class="nav-divider">
            {{ __('menus.menu') }}
        </li>
        <li class="nav-item ">
            <a class="nav-link {{ (request()->is('dashboard*')) ? 'active' : '' }}" href="{{ route('dashboard.index') }}">
                <i class="fa fa-home"></i>{{ __('dashboard.title') }}
            </a>
        </li>

        @if (!empty(auth()->user()->email_verified_at) && !empty(auth()->user()->mobile_verified_at) && auth()->user()->status == 1)
            <!-- <li class="nav-item ">
                <a class="nav-link {{ (request()->is('sender/statistics*')) ? 'active' : '' }}" href="{{route('merchant.panel.statistics')}}"><i class="fa-solid fa-chart-simple"></i>{{ __('parcel.statistics') }}</a>
            </li>  -->
            <li class="nav-item">
                <a class="nav-link {{ (request()->is('sender/order*')) ? 'active' : '' }}" href="{{route('merchant-panel.parcel.index')}}"><i class="fa fa-cube"></i>{{ __('menus.parcel') }}</a>
            </li>
            <li class="nav-item ">
                <a class="nav-link {{ (request()->is('sender/support*')) ? 'active' : '' }}" href="{{ route('merchant-panel.support.index') }}"><i class="fa fa-comments"></i>{{__('menus.support')}}</a>
            </li>

            <li class="nav-item ">
                <a class="nav-link {{ (request()->is('sender/mpesa/payment-history*')) ? 'active' : '' }}" href="{{ route('merchant-panel.mpesa.payment-history.index') }}">
                    <i class="fa fa-money-bill"></i>M-Pesa Payment History
                </a>
            </li>




            <li class="nav-item">
                <a class="nav-link {{ (request()->is('sender/settings/delivery-charges*')) ? 'active' : ' ' }}" href="{{route('merchant.delivery-charges.index')}}"><i class="fa fa-money-bill"></i>Delivery Charges
                </a>
            </li>

            



            <!-- <li class="nav-item">
                <a class="nav-link {{ (request()->is('sender/payment-request*','sender/invoice*','sender/payment/received*','sender/online-payment*','sender/invoice*')) ? 'active' : '' }}" href="#" data-toggle="collapse" aria-expanded="false" data-target="#accounts" aria-controls="accounts"><i class="fa fa-users"></i> {{ __('account.title') }}</a>
                <div id="accounts" class="{{ (request()->is('sender/payment-request*','sender/invoice*','sender/payment/received*','sender/online-payment*','sender/invoice*')) ? '' : 'collapse' }} submenu">
                    <ul class="nav flex-column">
                        <li class="nav-item ">
                            <a class="nav-link {{ (request()->is('sender/invoice*')) ? 'active' : '' }}" href="{{ route('merchant.panel.invoice.index') }}">{{__('menus.invoice')}}</a>
                        </li>
                    </ul>
                </div>
            </li> -->
            <!-- <li class="nav-item">
                <a class="nav-link {{ (request()->is('sender/customers*','sender/order*','sender/active-live-monitoring*','sender/passive-monitoring*')) ? 'active' : '' }}" href="#" data-toggle="collapse" aria-expanded="false" data-target="#orders" aria-controls="orders"><i class="fa fa-dolly"></i> {{ __('menus.parcel') }}</a>
                <div id="orders" class="{{ (request()->is('sender/customers*','sender/order*','sender/active-live-monitoring*','sender/passive-monitoring*')) ? '' : 'collapse' }} submenu">
                    <ul class="nav flex-column">
                        @if(auth()->user()->merchant->account_type == 2)
                            <li class="nav-item">
                                <a class="nav-link {{ (request()->is('sender/customers*')) ? 'active' : '' }}" href="{{ route('merchant-panel.customers.index') }}">
                                    {{ __('levels.customers') }}
                                </a>
                            </li>
                        @endif
                        <li class="nav-item">
                            <a class="nav-link {{ (request()->is('sender/order*')) ? 'active' : '' }}" href="{{route('merchant-panel.parcel.index')}}">{{ __('menus.parcel') }}</a>
                        </li>

                    </ul>
                </div>
            </li> -->

            <!-- <li class="nav-item ">
                <a class="nav-link {{ (request()->is('sender/received/parcels*')) ? 'active' : '' }}" href="{{route('merchant-panel.received.parcel.index')}}"><i class="fa fa-dolly"></i>{{ __('parcel.received_parcel') }}</a>
            </li> -->

            <!-- <li class="nav-item">
                <a class="nav-link {{ (request()->is('sender/reports/*','sender/accounts/*')) ? 'active' : '' }}" href="#" data-toggle="collapse" aria-expanded="false" data-target="#reports" aria-controls="reports"><i class="fas fa-print"></i>{{__('reports.title') }}</a>
                <div id="reports" class="{{ (request()->is('sender/reports*','sender/accounts/*')) ? '' : 'collapse' }} submenu">
                    <ul class="nav flex-column">
                        <li class="nav-item ">
                            <a class="nav-link {{ (request()->is('sender/reports/order-reports*','sender/reports/order-filter-reports*')) ? 'active' : '' }}" href="{{route('merchant-panel.parcel.reports')}}" aria-expanded="false" data-target="#submenu-1" aria-controls="submenu-1">{{ __('reports.parcel_reports') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ (request()->is('sender/reports/total-summery*','sender/reports/total-summery-filter*')) ? 'active' : '' }}" href="{{ route('merchant.total.summery') }}">{{ __('menus.parcel_total_summery') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ (request()->is('sender/accounts/account-transaction*')) ? 'active' : '' }}" href="{{route('merchant.accounts.account-transaction.index')}}">{{__('menus.account_transaction')}}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ (request()->is('sender/accounts/statements*')) ? 'active' : '' }}" href="{{route('merchant.accounts.statements.index')}}">{{__('menus.statements')}}</a>
                        </li>
                    </ul>
                </div>
            </li> -->
            <!-- <li class="nav-item">
                <a class="nav-link {{ (request()->is('sender/settings*','sender/shops*')) ? 'active' : '' }}" href="#" data-toggle="collapse" aria-expanded="false" data-target="#settings" aria-controls="settings"><i class="fa fa-fw fa-cogs"></i> {{ __('menus.settings') }}</a>
                <div id="settings" class="{{ (request()->is('sender/settings*','sender/shops*')) ? '' : 'collapse' }} submenu">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ (request()->is('sender/settings/cod-charges*')) ? 'active' : '' }}" href="{{route('merchant.cod-charges.index')}}">{{ __('menus.cod_charges') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ (request()->is('sender/settings/delivery-charges*')) ? 'active' : ' ' }}" href="{{route('merchant.delivery-charges.index')}}">{{ __('menus.delivery_charges') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ (request()->is('sender/settings/online-payment-setup*')) ? 'active' : ' ' }}" href="{{route('merchant.online.payment.setup.index')}}">{{ __('menus.online_payment_setup') }}</a>
                        </li>
                        <li class="nav-item ">
                            <a class="nav-link {{ (request()->is('sender/shops*')) ? 'active' : '' }}" href="{{route('merchant-panel.shops.index')}}"> {{ __('parcel.shop') }}</a>
                        </li>

                    </ul>
                </div>
            </li> -->
        @endif
    </ul>
</div>
<!-- end left sidebar -->
