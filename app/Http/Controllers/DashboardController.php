<?php

namespace App\Http\Controllers;

use App\Enums\UserType;
use App\Enums\ParcelStatus;
use App\Enums\ApprovalStatus;
use App\Enums\DeliveryType;
use App\Models\Backend\CourierStatement;
use App\Models\Backend\DeliverymanStatement;
use App\Models\Backend\MerchantStatement;
use App\Models\Backend\VatStatement;
use App\Models\User;
use App\Enums\StatementType;
use App\Models\Backend\Account;
use App\Models\Backend\BankTransaction;
use App\Models\Backend\DeliveryMan;
use App\Models\Backend\Merchant;
use App\Models\Backend\Parcel;
use App\Models\Backend\Payment;
use App\Models\Backend\Fraud;
use App\Models\MerchantShops;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Repositories\Dashboard\DashboardInterface;
 

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    protected $repo;
    public function __construct(DashboardInterface $repo)
    {
        $this->repo    = $repo;
    }
    public function index(Request $request)
    {
         
     
        if (Auth::user()->user_type == UserType::MERCHANT) {
            $t_parcel           = Parcel::where('merchant_id', Auth::user()->merchant->id)->count();
            $t_delivered        = Parcel::whereIn('status', [
                ParcelStatus::DELIVERED,
                ParcelStatus::PARTIAL_DELIVERED
            ])->where('merchant_id', Auth::user()->merchant->id)->count();
            $t_failure      = Parcel::wherein('status', [
                ParcelStatus::DELIVERY_FAILURE,
                ParcelStatus::DELIVERY_FAILED,
                ParcelStatus::PARCEL_CANCEL,
                ParcelStatus::DELIVERY_ATTEMPT1,
                ParcelStatus::DELIVERY_ATTEMPT2,
                ParcelStatus::DELIVERY_ATTEMPT3,
                ParcelStatus::DELIVERY_MAN_ASSIGN1,
                ParcelStatus::RETURN_TO_COURIER,
                ParcelStatus::RETURNING,
                ParcelStatus::TRANSIT_SENDING_PROVINCE,
                ParcelStatus::ON_THE_WAY_SENDING_PROVINCE,
                ParcelStatus::ARRIVED_TO_SENDING_HUB,
                ParcelStatus::RETURN_ASSIGN_TO_MERCHANT,
                ParcelStatus::RETURN_ASSIGN_TO_MERCHANT1,
                ParcelStatus::RETURN_MERCHANT_RE_SCHEDULE,
                ParcelStatus::RETURN_RECEIVED_BY_MERCHANT,
            ])->where('merchant_id', Auth::user()->merchant->id)->count();
            $t_shop             = MerchantShops::where('merchant_id', Auth::user()->merchant->id)->count();
            $t_parcel_bank      = Parcel::where('merchant_id', Auth::user()->merchant->id)->where('parcel_bank', 'on')->count();
            $merchant           = Merchant::where('id', Auth::user()->merchant->id)->first();
            $parcels            = Parcel::where('merchant_id', Auth::user()->merchant->id)->get();
            $t_assigned     = Parcel::where('merchant_id', Auth::user()->merchant->id)->wherein('status', [
                ParcelStatus::DELIVERY_MAN_ASSIGN,
                ParcelStatus::DELIVERY_RE_SCHEDULE,
                ParcelStatus::CONFIRMED,
                ParcelStatus::CONFIRMED_BOOKING,
                ParcelStatus::UNCONFIRMED,
                ParcelStatus::UNCONFIRMED_BOOKING,
            ])->count();
            $t_pending          = Parcel::where('merchant_id', Auth::user()->merchant->id)
                ->wherein('status', [
                    ParcelStatus::PENDING,
                    ParcelStatus::DELIVERY_MAN_ASSIGN_CANCEL,
                    ParcelStatus::DELIVERY_RE_SCHEDULE_CANCEL,
                ])->count();
            $t_processing  = Parcel::where('merchant_id', Auth::user()->merchant->id)->wherein('status', [
                ParcelStatus::PROCESSING,
                ParcelStatus::HEADING_TO_PICKUP_POINT,
                ParcelStatus::PICKED_UP,
                ParcelStatus::HEADING_TO_DELIVERY_POINT,
                ParcelStatus::DROP_OFF_CITY,
                ParcelStatus::DROP_OFf_HUB1,
                ParcelStatus::HEADING_TO_DROP_OFF,
                ParcelStatus::TRANSIT_OUT_CITY,
                ParcelStatus::ON_THE_WAY_TO_CITY,
                ParcelStatus::ARRIVED_AT_CITY,
                ParcelStatus::DELIVERY_MAN_ASSIGN_SECOND_PROVINCE,
            ])->count();

            $t_cash_collection   = 0;
            $t_selling_price     = 0;
            $t_liquid_fragile    = 0;
            $t_vat_amount        = 0;
            $t_delivery_charge   = 0;
            $t_cod_amount        = 0;
            $t_packaging         = 0;
            $t_delivery_amount   = 0;
            $t_current_payable   = 0;

            foreach ($parcels as $parcel) {
                if ($parcel->status != ParcelStatus::RETURN_RECEIVED_BY_MERCHANT) {
                    $t_cash_collection = $t_cash_collection + $parcel->cash_collection;
                    $t_selling_price   = $t_selling_price   + $parcel->selling_price;
                    $t_current_payable = $t_current_payable + $parcel->current_payable;
                }
                $t_liquid_fragile  = $t_liquid_fragile  + $parcel->liquid_fragile_amount;
                $t_vat_amount      = $t_vat_amount      + $parcel->vat_amount;
                $t_delivery_charge = $t_delivery_charge + $parcel->delivery_charge;
                $t_cod_amount      = $t_cod_amount      + $parcel->cod_amount;
                $t_packaging       = $t_packaging       + $parcel->packaging_amount;
                $t_delivery_amount = $t_delivery_amount + $parcel->total_delivery_amount;
            }

            $dates        = [];
            $totals       = [];
            $pendings     = [];
            $assigned_data = [];
            $processings = [];
            $delivers     = [];
            $failures      = [];
            for ($i = 7; $i >= 0; $i--) {
                $date = date('Y-m-d', strtotime(' -' . $i . ' day'));
                $total         = Parcel::where('merchant_id', Auth::user()->merchant->id)
                    ->where('updated_at', 'like', $date . '%')
                    ->count();
                $pending       = Parcel::where('merchant_id', Auth::user()->merchant->id)
                    ->wherein('status', [
                        ParcelStatus::PENDING,
                        ParcelStatus::DELIVERY_MAN_ASSIGN_CANCEL,
                        ParcelStatus::DELIVERY_RE_SCHEDULE_CANCEL,
                    ])
                    ->where('updated_at', 'like', $date . '%')
                    ->count();
                $assigned = Parcel::where('merchant_id', Auth::user()->merchant->id)
                    ->wherein('status', [
                        ParcelStatus::DELIVERY_MAN_ASSIGN,
                        ParcelStatus::DELIVERY_RE_SCHEDULE,
                        ParcelStatus::CONFIRMED,
                        ParcelStatus::CONFIRMED_BOOKING,
                        ParcelStatus::UNCONFIRMED,
                        ParcelStatus::UNCONFIRMED_BOOKING,
                    ])
                    ->where('updated_at', 'like', $date . '%')
                    ->count();
                $processing = Parcel::where('merchant_id', Auth::user()->merchant->id)
                    ->wherein('status', [
                        ParcelStatus::DELIVERY_MAN_ASSIGN,
                        ParcelStatus::DELIVERY_RE_SCHEDULE,
                        ParcelStatus::CONFIRMED,
                        ParcelStatus::CONFIRMED_BOOKING,
                        ParcelStatus::UNCONFIRMED,
                        ParcelStatus::UNCONFIRMED_BOOKING,
                    ])
                    ->where('updated_at', 'like', $date . '%')
                    ->count();
                $delivered     = Parcel::where('merchant_id', Auth::user()->merchant->id)
                    ->where('status', ParcelStatus::DELIVERED)
                    ->where('updated_at', 'like', $date . '%')
                    ->count();

                $failure      = Parcel::where('merchant_id', Auth::user()->merchant->id)
                    ->where('status', ParcelStatus::RETURN_RECEIVED_BY_MERCHANT)
                    ->where('updated_at', 'like', $date . '%')
                    ->count();
                array_push($dates, $date);
                array_push($totals, $total);
                array_push($pendings, $pending);
                array_push($assigned_data, $assigned);
                array_push($processings, $processing);
                array_push($delivers, $delivered);
                array_push($failures, $failure);
            }

            $t_sale         = Parcel::where('merchant_id', Auth::user()->merchant->id)
                ->whereIn('status', [ParcelStatus::DELIVERED, ParcelStatus::PARTIAL_DELIVERED])
                ->sum('total_delivery_amount');
            $t_sale         += Parcel::where('merchant_id', Auth::user()->merchant->id)
                ->whereIn('status', [ParcelStatus::DELIVERED, ParcelStatus::PARTIAL_DELIVERED])
                ->sum('vat_amount');
            $ts_vat         = Parcel::where('merchant_id', Auth::user()->merchant->id)->whereIn('status', [ParcelStatus::DELIVERED, ParcelStatus::PARTIAL_DELIVERED])->sum('vat_amount');
            $t_delivery_fee = Parcel::where('merchant_id', Auth::user()->merchant->id)->whereIn('status', [ParcelStatus::DELIVERED, ParcelStatus::PARTIAL_DELIVERED])->sum('total_delivery_amount');

            $t_balance_proc = Payment::where('merchant_id', Auth::user()->merchant->id)->where('status', ApprovalStatus::PENDING)->sum('amount');
            $t_balance_paid = Payment::where('merchant_id', Auth::user()->merchant->id)->where('status', ApprovalStatus::PROCESSED)->sum('amount');
            $t_request      = Payment::where('merchant_id', Auth::user()->merchant->id)->count();

            $fromTo                         = $this->repo->FromTo($request); //from/to date
            //pie charts total
            $piedata = [];
            $piedata['total_parcels']          = Parcel::where(['merchant_id' => Auth::user()->merchant->id])
                ->count();
            $piedata['total_pending']          = Parcel::where('merchant_id', Auth::user()->merchant->id)
                ->wherein('status', [
                    ParcelStatus::PENDING,
                    ParcelStatus::DELIVERY_MAN_ASSIGN_CANCEL,
                    ParcelStatus::DELIVERY_RE_SCHEDULE_CANCEL,
                ])
                ->count();
            $piedata['total_assigned'] = Parcel::where('merchant_id', Auth::user()->merchant->id)
                ->wherein('status', [
                    ParcelStatus::DELIVERY_MAN_ASSIGN,
                    ParcelStatus::DELIVERY_RE_SCHEDULE,
                    ParcelStatus::CONFIRMED,
                    ParcelStatus::CONFIRMED_BOOKING,
                    ParcelStatus::UNCONFIRMED,
                    ParcelStatus::UNCONFIRMED_BOOKING,
                ])
                ->count();
            $piedata['total_delivered']        = Parcel::where('merchant_id', Auth::user()->merchant->id)
                ->whereIn('status', [
                    ParcelStatus::DELIVERED,
                    ParcelStatus::PARTIAL_DELIVERED
                ])
                ->count();
            $piedata['total_processing']           = Parcel::where('merchant_id', Auth::user()->merchant->id)
                ->wherein('status', [
                    ParcelStatus::PROCESSING,
                    ParcelStatus::HEADING_TO_PICKUP_POINT,
                    ParcelStatus::PICKED_UP,
                    ParcelStatus::HEADING_TO_DELIVERY_POINT,
                    ParcelStatus::DROP_OFF_CITY,
                    ParcelStatus::DROP_OFf_HUB1,
                    ParcelStatus::HEADING_TO_DROP_OFF,
                    ParcelStatus::TRANSIT_OUT_CITY,
                    ParcelStatus::ON_THE_WAY_TO_CITY,
                    ParcelStatus::ARRIVED_AT_CITY,
                    ParcelStatus::DELIVERY_MAN_ASSIGN_SECOND_PROVINCE,
                ])
                ->count();
            $piedata['total_failure']           = Parcel::where('merchant_id', Auth::user()->merchant->id)
                ->wherein('status', [
                    ParcelStatus::DELIVERY_FAILURE,
                    ParcelStatus::DELIVERY_FAILED,
                    ParcelStatus::PARCEL_CANCEL,
                    ParcelStatus::DELIVERY_ATTEMPT1,
                    ParcelStatus::DELIVERY_ATTEMPT2,
                    ParcelStatus::DELIVERY_ATTEMPT3,
                    ParcelStatus::DELIVERY_MAN_ASSIGN1,
                    ParcelStatus::RETURN_TO_COURIER,
                    ParcelStatus::RETURNING,
                    ParcelStatus::TRANSIT_SENDING_PROVINCE,
                    ParcelStatus::ON_THE_WAY_SENDING_PROVINCE,
                    ParcelStatus::ARRIVED_TO_SENDING_HUB,
                    ParcelStatus::RETURN_ASSIGN_TO_MERCHANT,
                    ParcelStatus::RETURN_ASSIGN_TO_MERCHANT1,
                    ParcelStatus::RETURN_MERCHANT_RE_SCHEDULE,
                    ParcelStatus::RETURN_RECEIVED_BY_MERCHANT,
                ])
                ->count();
            return view(
                'backend.merchant_panel.dashboard',
                compact(
                    't_parcel',
                    't_delivered',
                    't_assigned',
                    't_processing',
                    't_failure',
                    't_sale',
                    't_delivery_fee',
                    'ts_vat',
                    't_balance_proc',
                    't_balance_paid',
                    't_request',
                    'merchant',
                    't_shop',
                    't_parcel_bank',
                    't_pending',
                    't_cash_collection',
                    't_selling_price',
                    't_liquid_fragile',
                    't_vat_amount',
                    't_delivery_charge',
                    't_cod_amount',
                    't_packaging',
                    't_delivery_amount',
                    't_current_payable',
                    'dates',
                    'totals',
                    'pendings',
                    'delivers',
                    'assigned_data',
                    'processings',
                    'failures',
                    'piedata',
                    'request'
                )
            );
        } elseif (Auth::user()->user_type == UserType::DELIVERYMAN) {
            if (!empty($request->parcel_history)) :
                $parcels =  Parcel::orderByDesc('id')->where(function ($query) {
                    if (auth()->user()->deliveryman) {
                        $query->whereHas('parcelEvent', function ($queryParcelEvent) {
                            if (auth()->user()->deliveryman->id) {
                                $queryParcelEvent->where(['delivery_man_id' => auth()->user()->deliveryman->id]);
                                $queryParcelEvent->orWhere(['pickup_man_id' => auth()->user()->deliveryman->id]);
                            }
                        });
                    }
                })->orderByDesc('id')->paginate(10);
                return view('backend.deliveryman_panel.deliveryman_assigned_parcels', compact('request', 'parcels'));
            endif;
            $parcels =  Parcel::where(function ($query) {
                $query->orWhereHas('parcelEvent', function ($queryParcelEvent) {
                    if (auth()->user()->deliveryman->id) {
                        $queryParcelEvent->where(['delivery_man_id' => auth()->user()->deliveryman->id]);
                    }
                });
            })->whereIn('status', [ParcelStatus::PENDING, ParcelStatus::DELIVERY_MAN_ASSIGN])->orderByDesc('id')->paginate(10);
            return view('backend.deliveryman_panel.pending_parcels', compact('request', 'parcels'));
        } else {
            $c_income       = CourierStatement::where(function ($query) use ($request) {
                $query->whereHas('parcel', function ($query) {
                    if (request()->province_id) :
                        $query->where('from_state_id', request()->province_id);
                    endif;
                    if (request()->merchant_id) :
                        $query->where('merchant_id', request()->merchant_id);
                    endif;
                });
            })->whereNot('parcel_id', null)
                ->where('type', StatementType::INCOME)
                ->whereBetween('updated_at', $this->repo->FromTo($request))
                ->sum('amount');

            $c_expense      = CourierStatement::where(function ($query) use ($request) {
                $query->whereHas('parcel', function ($query) {
                    if (request()->province_id) :
                        $query->where('from_state_id', request()->province_id);
                    endif;
                    if (request()->merchant_id) :
                        $query->where('merchant_id', request()->merchant_id);
                    endif;
                });
            })->whereNot('parcel_id', null)
                ->where('type', StatementType::EXPENSE)
                ->whereBetween('updated_at', $this->repo->FromTo($request))
                ->sum('amount');

            $d_income = DeliverymanStatement::where(function ($query) use ($request) {
                $query->whereHas('parcel', function ($query) {
                    if (request()->province_id) :
                        $query->where('from_state_id', request()->province_id);
                    endif;
                    if (request()->merchant_id) :
                        $query->where('merchant_id', request()->merchant_id);
                    endif;
                });
            })->where('type', StatementType::INCOME)
                ->whereBetween('updated_at', $this->repo->FromTo($request))
                ->sum('amount');

            $d_expense      = DeliverymanStatement::where(function ($query) use ($request) {
                $query->whereHas('parcel', function ($query) {
                    if (request()->province_id) :
                        $query->where('from_state_id', request()->province_id);
                    endif;
                    if (request()->merchant_id) :
                        $query->where('merchant_id', request()->merchant_id);
                    endif;
                });
            })->where('type', StatementType::EXPENSE)
                ->whereBetween('updated_at', $this->repo->FromTo($request))
                ->sum('amount');

            $m_income       = MerchantStatement::where(function ($query) use ($request) {
                $query->whereHas('parcel', function ($query) {
                    if (request()->province_id) :
                        $query->where('from_state_id', request()->province_id);
                    endif;
                    if (request()->merchant_id) :
                        $query->where('merchant_id', request()->merchant_id);
                    endif;
                });
            })->where('type', StatementType::INCOME)
                ->whereBetween('updated_at', $this->repo->FromTo($request))
                ->sum('amount');

            $m_expense      = MerchantStatement::where(function ($query) use ($request) {
                $query->whereHas('parcel', function ($query) {
                    if (request()->province_id) :
                        $query->where('from_state_id', request()->province_id);
                    endif;
                    if (request()->merchant_id) :
                        $query->where('merchant_id', request()->merchant_id);
                    endif;
                });
            })->where('type', StatementType::EXPENSE)
                ->whereBetween('updated_at', $this->repo->FromTo($request))
                ->sum('amount');

            $v_income       = VatStatement::where(function ($query) use ($request) {
                $query->whereHas('parcel', function ($query) {
                    if (request()->province_id) :
                        $query->where('from_state_id', request()->province_id);
                    endif;
                    if (request()->merchant_id) :
                        $query->where('merchant_id', request()->merchant_id);
                    endif;
                });
            })->where('type', StatementType::INCOME)
                ->whereBetween('updated_at', $this->repo->FromTo($request))
                ->sum('amount');

            $v_expense      = VatStatement::where(function ($query) use ($request) {
                $query->whereHas('parcel', function ($query) {
                    if (request()->province_id) :
                        $query->where('from_state_id', request()->province_id);
                    endif;
                    if (request()->merchant_id) :
                        $query->where('merchant_id', request()->merchant_id);
                    endif;
                });
            })->where('type', StatementType::EXPENSE)
                ->whereBetween('updated_at', $this->repo->FromTo($request))
                ->sum('amount');

            $b_income       = BankTransaction::where('type', StatementType::INCOME)
                ->whereBetween('updated_at', $this->repo->FromTo($request))
                ->sum('amount');

            $b_expense      = BankTransaction::where('type', StatementType::EXPENSE)
                ->whereBetween('updated_at', $this->repo->FromTo($request))
                ->sum('amount');

            $data['dashboard'] = 'dashboard';

            // Recent Parcels
            $data['recent_parcels']             = Parcel::where(function ($query) use ($request) {
                if (request()->province_id) :
                    $query->where('from_state_id', request()->province_id);
                endif;
                if (request()->merchant_id) :
                    $query->where('merchant_id', request()->merchant_id);
                endif;
                if (request()) :
                    $query->whereBetween('updated_at', $this->repo->FromTo($request));
                endif;
            })->orderByDesc('id')
                ->limit(5)
                ->get();

            //Total Parcel
            $data['total_parcel']               = Parcel::where(function ($query) {
                if (request()->province_id) :
                    $query->where('from_state_id', request()->province_id);
                endif;
                if (request()->merchant_id) :
                    $query->where('merchant_id', request()->merchant_id);
                endif;
            })->whereBetween('updated_at', $this->repo->FromTo($request))->count();

            $marketplaceQuery = Parcel::where('status', ParcelStatus::MARKETPLACE_DELIVERED)
                ->where(function ($query) {
                    if (request()->province_id) :
                        $query->where('from_state_id', request()->province_id);
                    endif;
                    if (request()->merchant_id) :
                        $query->where('merchant_id', request()->merchant_id);
                    endif;
                })->whereBetween('updated_at', $this->repo->FromTo($request));

            $data['marketplace_delivered']       = (clone $marketplaceQuery)->count();
            $data['marketplace_delivery_charge'] = (clone $marketplaceQuery)->sum('final_paid_amount');
            $data['marketplace_commission']      = (clone $marketplaceQuery)->sum('commission_amount');
            $data['marketplace_rider_earnings']  = (clone $marketplaceQuery)->sum('rider_earning');
            $data['marketplace_platform_earning'] = (clone $marketplaceQuery)->sum('platform_total_earning');

            // Total user
            if (count($request->all())) {
                $data['total_user']                 = User::where(function ($query) {
                    $query->where('user_type', UserType::ADMIN);
                    if (request()->province_id) {
                        $query->where('province_id', request()->province_id);
                    }
                })->whereBetween('updated_at', $this->repo->FromTo($request))->count();
            } else {
                $data['total_user']                 = User::where('user_type', UserType::ADMIN)->count();
            }

            //Total merchant
            if (count($request->all())) {
                $data['total_merchant']             = Merchant::where(function ($query) {
                    $query->whereHas('user', function ($query) {
                        if (request()->province_id) {
                            $query->where('province_id', request()->province_id);
                        }
                    });
                    if (request()->merchant_id) {
                        $query->where('id', request()->merchant_id);
                    }
                })->whereBetween('updated_at', $this->repo->FromTo($request))->count();
            } else {
                $data['total_merchant']             = Merchant::count();
            }

            //total delivery man
            if (count($request->all())) {
                $data['total_delivery_man']         = DeliveryMan::where(function ($query) {
                    $query->whereHas('user', function ($query) {
                        if (request()->province_id) {
                            $query->where('province_id', request()->province_id);
                        }
                    });
                })->whereBetween('updated_at', $this->repo->FromTo($request))->count();
            } else {
                $data['total_delivery_man']         = DeliveryMan::count();
            }

            //total accounts
            if (count($request->all())) {
                $data['total_accounts']             = Account::whereBetween('updated_at', $this->repo->FromTo($request))->count();
            } else {
                $data['total_accounts']             = Account::count();
            }

            //status wise parcel count
            $data['total_deliveryman_assigned'] = $this->repo->parcelPosition($request, ParcelStatus::DELIVERY_MAN_ASSIGN, $this->repo->FromTo($request))
                ->where(function ($query) {
                    if (request()->province_id) :
                        $query->where('province_id', request()->province_id);
                    endif;
                    if (request()->merchant_id) :
                        $query->where('merchant_id', request()->merchant_id);
                    endif;
                })->count();

            $data['total_failure']     = $this->repo->parcelPositionDeliveryFailure($request, $this->repo->FromTo($request))->count();

            $data['total_deliverd']             = $this->repo->parcelPosition($request, ParcelStatus::DELIVERED, $this->repo->FromTo($request))
                ->where(function ($query) {
                    if (request()->province_id) :
                        $query->where('province_id', request()->province_id);
                    endif;
                    if (request()->merchant_id) :
                        $query->where('merchant_id', request()->merchant_id);
                    endif;
                })->count();
            //end status wise parcel count
            //end salary

            $dates                           =   $this->repo->Dates($request); // 7days
            $data['incomeDates']             =   $dates;
            $data['expenseDates']            =   $dates;
            $data['merchantRevDates']        =   $dates;
            $data['DeliverymanRevDates']     =   $dates;

            $fromTo                         = $this->repo->FromTo($request); //from/to date
            $data['income']                 = $this->repo->income($fromTo, $request);
            $data['expense']                = $this->repo->expense($fromTo, $request);
            $data['merchantIncome']         = $this->repo->merchantIncome($fromTo, $request);
            $data['merchantExpense']        = $this->repo->merchantExpense($fromTo, $request);
            $data['deliverymanIncome']      = $this->repo->deliverymanIncome($fromTo, $request);
            $data['deliverymanExpense']     = $this->repo->deliverymanExpense($fromTo, $request);
            $data['bank_transactions']      = $this->repo->bankTransaction($fromTo, $request); // No
            $data['courier_income']         = $this->repo->courierIncome($fromTo, $request);
            $data['courier_expense']        = $this->repo->courierExpense($fromTo, $request);
            return view('backend.dashboard', compact(
                'c_income',
                'c_expense',
                'd_income',
                'd_expense',
                'm_income',
                'm_expense',
                'v_income',
                'v_expense',
                'b_income',
                'b_expense',
                'data',
                'request'
            ));
        }
    }

    public function searchCharts(Request $request)
    {
        $data    = [];
        $data['dates']                      = $this->repo->dates($request);
        $fromTo                             = $this->repo->FromTo($request);
        if ($request->type     == 'income_expense') :
            $data['income']                 = $this->repo->income($fromTo);
            $data['expense']                = $this->repo->expense($fromTo);
        elseif ($request->type == 'merchant') :
            $data['merchantIncome']         = $this->repo->merchantIncome($fromTo);
            $data['merchantExpense']        = $this->repo->merchantExpense($fromTo);
        elseif ($request->type == 'deliveryman') :
            $data['deliverymanIncome']      = $this->repo->deliverymanIncome($fromTo);
            $data['deliverymanExpense']     = $this->repo->deliverymanExpense($fromTo);
        endif;

        return $data;
    }


    public function merchantDashboardFilter(Request $request)
    {
        $from = date('Y-m-d');
        $to   = date('Y-m-d');
        if ($request->date) {
            $date = explode('To', $request->date);
            if (is_array($date)) {
                $from   = Carbon::parse(trim($date[0]))->startOfDay()->toDateTimeString();
                $to     = Carbon::parse(trim($date[1]))->endOfDay()->toDateTimeString();
            }
        }

        $merchant       = Merchant::where('id', Auth::user()->merchant->id)->first();
        $t_fraud        = Fraud::where('created_by', Auth::user()->id)->count();
        $t_shop         = MerchantShops::where('merchant_id', Auth::user()->merchant->id)->count();

        $t_parcel       = Parcel::where(function ($query) {
            if (request()->delivery_type_id) :
                $query->where('delivery_type_id', request()->delivery_type_id);
            endif;
            if (request()->shipping_type) :
                $query->where('delivery_type_id', request()->shipping_type);
            endif;
        })->where('merchant_id', Auth::user()->merchant->id)->whereBetween('updated_at', [$from, $to])->count();
        $t_delivered    = Parcel::where('status', ParcelStatus::DELIVERED)->where('merchant_id', Auth::user()->merchant->id)->whereBetween('updated_at', [$from, $to])->count();
        $t_return       = Parcel::where('status', ParcelStatus::RETURN_RECEIVED_BY_MERCHANT)->where('merchant_id', Auth::user()->merchant->id)->whereBetween('updated_at', [$from, $to])->count();
        $t_parcel_bank  = Parcel::where('merchant_id', Auth::user()->merchant->id)->where('parcel_bank', 'on')->whereBetween('updated_at', [$from, $to])->count();
        $t_sale         = Parcel::where('merchant_id', Auth::user()->merchant->id)
            ->whereBetween('updated_at', [$from, $to])
            ->where(function ($query) {
                $query->where('status', ParcelStatus::DELIVERED)
                    ->orWhere('status', ParcelStatus::PARTIAL_DELIVERED);
            })
            ->sum('total_delivery_amount');
        $t_sale         += Parcel::where('merchant_id', Auth::user()->merchant->id)
            ->whereBetween('updated_at', [$from, $to])
            ->where(function ($query) {
                $query->where('status', ParcelStatus::DELIVERED)
                    ->orWhere('status', ParcelStatus::PARTIAL_DELIVERED);
            })
            ->sum('vat_amount');
        $t_delivery_fee = Parcel::where('merchant_id', Auth::user()->merchant->id)->whereBetween('updated_at', [$from, $to])->where('status', ParcelStatus::DELIVERED)->orwhere('status', ParcelStatus::PARTIAL_DELIVERED)->sum('total_delivery_amount');
        $t_balance_proc = Payment::where('merchant_id', Auth::user()->merchant->id)->where('status', ApprovalStatus::PENDING)->whereBetween('updated_at', [$from, $to])->sum('amount');
        $t_balance_paid = Payment::where('merchant_id', Auth::user()->merchant->id)->where('status', ApprovalStatus::PROCESSED)->whereBetween('updated_at', [$from, $to])->sum('amount');
        $t_request      = Payment::where('merchant_id', Auth::user()->merchant->id)->whereBetween('updated_at', [$from, $to])->count();
        $parcels        = Parcel::where('merchant_id', Auth::user()->merchant->id)->whereBetween('updated_at', [$from, $to])->get();
        $t_cancelled    = Parcel::where('merchant_id', Auth::user()->merchant->id)->where('status', ParcelStatus::PARCEL_CANCEL)->whereBetween('updated_at', [$from, $to])->count();
        $t_pending      = Parcel::where('merchant_id', Auth::user()->merchant->id)->where('status', ParcelStatus::PENDING)->whereBetween('updated_at', [$from, $to])->count();
        $t_confirmed    = Parcel::where('merchant_id', Auth::user()->merchant->id)->where('status', ParcelStatus::CONFIRMED)->whereBetween('updated_at', [$from, $to])->count();
        $t_unconfirmed  = Parcel::where('merchant_id', Auth::user()->merchant->id)->where('status', ParcelStatus::UNCONFIRMED)->whereBetween('updated_at', [$from, $to])->count();

        $ts_vat         = Parcel::where('merchant_id', Auth::user()->merchant->id)->whereIn('status', [ParcelStatus::DELIVERED, ParcelStatus::PARTIAL_DELIVERED])->whereBetween('updated_at', [$from, $to])->sum('vat_amount');

        $t_cash_collection   = 0;
        $t_selling_price     = 0;
        $t_liquid_fragile    = 0;
        $t_vat_amount        = 0;
        $t_delivery_charge   = 0;
        $t_cod_amount        = 0;
        $t_packaging         = 0;
        $t_delivery_amount   = 0;
        $t_current_payable   = 0;

        foreach ($parcels as $parcel) {
            if ($parcel->status != ParcelStatus::RETURN_RECEIVED_BY_MERCHANT) {
                $t_cash_collection = $t_cash_collection + $parcel->cash_collection;
                $t_selling_price   = $t_selling_price   + $parcel->selling_price;
                $t_current_payable = $t_current_payable + $parcel->current_payable;
            }
            $t_liquid_fragile  = $t_liquid_fragile  + $parcel->liquid_fragile_amount;
            $t_vat_amount      = $t_vat_amount      + $parcel->vat_amount;
            $t_delivery_charge = $t_delivery_charge + $parcel->delivery_charge;
            $t_cod_amount      = $t_cod_amount      + $parcel->cod_amount;
            $t_packaging       = $t_packaging       + $parcel->packaging_amount;
            $t_delivery_amount = $t_delivery_amount + $parcel->total_delivery_amount;
        }

        $dates        = [];
        $totals       = [];
        $pendings     = [];
        $delivers     = [];
        $par_delivers = [];
        $returns      = [];


        $new_from_date = substr($from, 0, 10);
        $new_to_date   = substr($to, 0, 10);
        $time          = strtotime($new_to_date);
        $diff          = Carbon::parse($new_from_date)->diffInDays($new_to_date);

        for ($i = $diff; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime(' -' . $i . ' day', $time));
            $total         = Parcel::where('merchant_id', Auth::user()->merchant->id)->where('updated_at', 'like', $date . '%')->count();
            $pending       = Parcel::where('merchant_id', Auth::user()->merchant->id)->where('status', ParcelStatus::PENDING)->where('updated_at', 'like', $date . '%')->count();
            $delivered     = Parcel::where('merchant_id', Auth::user()->merchant->id)->where('status', ParcelStatus::DELIVERED)->where('updated_at', 'like', $date . '%')->count();
            $par_delivered = Parcel::where('merchant_id', Auth::user()->merchant->id)->where('status', ParcelStatus::PARTIAL_DELIVERED)->where('updated_at', 'like', $date . '%')->count();
            $returned      = Parcel::where('merchant_id', Auth::user()->merchant->id)->where('status', ParcelStatus::RETURN_RECEIVED_BY_MERCHANT)->where('updated_at', 'like', $date . '%')->count();

            array_push($dates, $date);
            array_push($totals, $total);
            array_push($pendings, $pending);
            array_push($delivers, $delivered);
            array_push($par_delivers, $par_delivered);
            array_push($returns, $returned);
        }


        //pie charts total
        $piedata = [];
        $piedata['total_parcels']          = Parcel::where(['merchant_id' => Auth::user()->merchant->id])->whereBetween('updated_at', [$from, $to])->count();
        $piedata['total_pending']          = Parcel::where(['merchant_id' => Auth::user()->merchant->id, 'status' => ParcelStatus::PENDING])->whereBetween('updated_at', [$from, $to])->count();
        $piedata['total_delivered']        = Parcel::where(['merchant_id' => Auth::user()->merchant->id, 'status' => ParcelStatus::DELIVERED])->whereBetween('updated_at', [$from, $to])->count();
        $piedata['total_partial_delivered'] = Parcel::where(['merchant_id' => Auth::user()->merchant->id, 'status' => ParcelStatus::PARTIAL_DELIVERED])->whereBetween('updated_at', [$from, $to])->count();
        $piedata['total_return']           = Parcel::where(['merchant_id' => Auth::user()->merchant->id, 'status' => ParcelStatus::RETURN_RECEIVED_BY_MERCHANT])->whereBetween('updated_at', [$from, $to])->count();


        return view(
            'backend.merchant_panel.dashboard',
            compact(
                'request',
                't_parcel',
                't_pending',
                't_confirmed',
                't_unconfirmed',
                't_cancelled',
                't_delivered',
                't_return',
                't_sale',
                'ts_vat',
                't_delivery_fee',
                't_balance_proc',
                't_balance_paid',
                't_request',
                'merchant',
                't_fraud',
                't_shop',
                't_parcel_bank',
                't_cash_collection',
                't_selling_price',
                't_liquid_fragile',
                't_vat_amount',
                't_delivery_charge',
                't_cod_amount',
                't_packaging',
                't_delivery_amount',
                't_current_payable',
                'dates',
                'totals',
                'pendings',
                'delivers',
                'par_delivers',
                'returns',
                'piedata'
            )
        );
    }


    public function analytics(Request $request)
    {
        $d      = $request->filter_date ?? Carbon::now()->format('m/d/Y') . ' To ' . Carbon::now()->format('m/d/Y');
        $date   = $this->repo->analyticsFromTo($d);
        $request['filter_date']  = $d;
        $data = [];
        $data['request'] = $request;
        $data['total_orders']             = $this->repo->analytics($request)
            ->where('merchant_id', Auth::user()->merchant->id)
            ->whereBetween('updated_at', [$date['from'], $date['to']])
            ->count();
        $data['total_delivered']          = $this->repo->analytics($request)
            ->whereIn('status', [ParcelStatus::DELIVERED, ParcelStatus::PARTIAL_DELIVERED])
            ->where('merchant_id', Auth::user()->merchant->id)
            ->whereBetween('updated_at', [$date['from'], $date['to']])
            ->count();
        $data['total_cancelled']          =  $this->repo->analytics($request)
            ->where('status', ParcelStatus::PARCEL_CANCEL)
            ->where('merchant_id', Auth::user()->merchant->id)
            ->whereBetween('updated_at', [$date['from'], $date['to']])
            ->count();

        $data['total_distance']           = $this->repo->analytics($request)
            ->where('merchant_id', Auth::user()->merchant->id)
            ->whereBetween('updated_at', [$date['from'], $date['to']])
            ->sum('distance_km');

        $data['total_inside_distance']    = $this->repo->analytics($request)
            ->where('merchant_id', Auth::user()->merchant->id)
            ->where('delivery_type_id', DeliveryType::SAMEDAY)
            ->whereBetween('updated_at', [$date['from'], $date['to']])
            ->sum('distance_km');

        $data['total_outside_distance']   = $this->repo->analytics($request)
            ->where('merchant_id', Auth::user()->merchant->id)
            ->where('delivery_type_id', DeliveryType::SUBCITY)
            ->whereBetween('updated_at', [$date['from'], $date['to']])
            ->sum('distance_km');

        $data['total_expense']            = $this->repo->analytics($request)
            ->where('merchant_id', Auth::user()->merchant->id)
            ->whereBetween('updated_at', [$date['from'], $date['to']])
            ->sum('total_delivery_amount');

        $data['total_inside_expense']     = $this->repo->analytics($request)
            ->where('merchant_id', Auth::user()->merchant->id)
            ->where('delivery_type_id', DeliveryType::SAMEDAY)
            ->whereBetween('updated_at', [$date['from'], $date['to']])
            ->sum('total_delivery_amount');

        $data['total_outside_expense']    = $this->repo->analytics($request)
            ->where('merchant_id', Auth::user()->merchant->id)
            ->where('delivery_type_id', DeliveryType::SUBCITY)
            ->whereBetween('updated_at', [$date['from'], $date['to']])
            ->sum('total_delivery_amount');

        $data['seven_days_order']     = $this->repo->merchant_seven_days_order_summery();
        $data['current_month_order']  = $this->repo->merchant_monthly_order_summery();

        return view('backend.merchant_panel.analytics', $data);
    }
}
