<?php

namespace App\Http\Controllers\Backend;

use App\Enums\ApprovalStatus;
use App\Enums\DeliveryType;
use App\Enums\ParcelStatus;
use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Models\Backend\DeliverymanStatement;
use App\Models\Backend\Merchant;
use App\Models\Backend\Parcel;
use App\Models\Backend\Payment;
use App\Repositories\Dashboard\DashboardInterface;
use App\Repositories\DeliveryMan\DeliveryManInterface;
use App\Repositories\Merchant\MerchantInterface;
use App\Repositories\Reports\TotalSummeryReport\TotalSummeryReportInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StatisticsController extends Controller
{
    protected $repo, $summeryRepo, $merchantRepo, $deliverymanRepo, $data = [];
    public function __construct(
        DashboardInterface $repo,
        TotalSummeryReportInterface $summeryRepo,
        MerchantInterface           $merchantRepo,
        DeliveryManInterface        $deliverymanRepo
    ) {
        $this->repo            = $repo;
        $this->summeryRepo     = $summeryRepo;
        $this->merchantRepo    = $merchantRepo;
        $this->deliverymanRepo = $deliverymanRepo;
    }

    public  function merchantStatistics(Request $request)
    {

        //it is date wise 
        $request_date        = $request->date ?? Carbon::now()->format('Y-m-d') . ' To ' . Carbon::now()->format('Y-m-d');
        $request['date']     = $request_date;
        $date                = $this->repo->FromTo($request);

        $td_delivered        = Parcel::where('merchant_id', Auth::user()->merchant->id)
            ->whereIn('status', [ParcelStatus::DELIVERED, ParcelStatus::PARTIAL_DELIVERED])
            ->whereBetween('updated_at', [$date['from'], $date['to']])
            ->count();
        $t_returned_merchant = Parcel::where('merchant_id', Auth::user()->merchant->id)
            ->where('status', ParcelStatus::RETURNED_MERCHANT)
            ->whereBetween('updated_at', [$date['from'], $date['to']])
            ->count();

        $t_pending           = Parcel::where('merchant_id', Auth::user()->merchant->id)
            ->whereIn('status', [
                ParcelStatus::PENDING,
                ParcelStatus::DELIVERY_MAN_ASSIGN_CANCEL,
                ParcelStatus::DELIVERY_RE_SCHEDULE_CANCEL
            ])
            ->whereBetween('updated_at', [$date['from'], $date['to']])
            ->count();

        $t_in_transit        = Parcel::where('merchant_id', Auth::user()->merchant->id)
            ->whereIn('status', [
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
            ->whereBetween('updated_at', [$date['from'], $date['to']])
            ->count();
        $t_balance_pending   = Payment::where('merchant_id', Auth::user()->merchant->id)
            ->whereIn('status', [
                ParcelStatus::PENDING,
                ParcelStatus::DELIVERY_MAN_ASSIGN_CANCEL,
                ParcelStatus::DELIVERY_RE_SCHEDULE_CANCEL
            ])
            ->whereBetween('updated_at', [$date['from'], $date['to']])
            ->sum('amount');

        //delivered parcel
        $total_delivered_count        = Parcel::where('merchant_id', Auth::user()->merchant->id)
            ->whereIn('status', [ParcelStatus::DELIVERED, ParcelStatus::PARTIAL_DELIVERED])
            ->whereBetween('updated_at', [$date['from'], $date['to']])
            ->count();
        $t_delivered_collected_amount = Parcel::where('merchant_id', Auth::user()->merchant->id)
            ->whereIn('status', [ParcelStatus::DELIVERED, ParcelStatus::PARTIAL_DELIVERED])
            ->whereBetween('updated_at', [$date['from'], $date['to']])
            ->sum('cash_collection');
        $delivered_delivery_charge    = Parcel::where('merchant_id', Auth::user()->merchant->id)
            ->whereIn('status', [ParcelStatus::DELIVERED, ParcelStatus::PARTIAL_DELIVERED])
            ->whereBetween('updated_at', [$date['from'], $date['to']])
            ->sum('total_delivery_amount');
        $delivered_cod                = Parcel::where('merchant_id', Auth::user()->merchant->id)
            ->whereIn('status', [ParcelStatus::DELIVERED, ParcelStatus::PARTIAL_DELIVERED])
            ->whereBetween('updated_at', [$date['from'], $date['to']])
            ->sum('cod_amount');
        //end delivered parcel

        //failure delivered parcel
        $total_par_delivered_count        = Parcel::where('merchant_id', Auth::user()->merchant->id)
            ->whereIn('status', [
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
            ->whereBetween('updated_at', [$date['from'], $date['to']])
            ->count();
        $t_par_delivered_collected_amount = Parcel::where('merchant_id', Auth::user()->merchant->id)
            ->whereIn('status', [
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
            ->whereBetween('updated_at', [$date['from'], $date['to']])
            ->sum('cash_collection');
        $par_delivered_delivery_charge    = Parcel::where('merchant_id', Auth::user()->merchant->id)
            ->whereIn('status', [
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
            ->whereBetween('updated_at', [$date['from'], $date['to']])
            ->sum('total_delivery_amount');
        $par_delivered_cod                = Parcel::where('merchant_id', Auth::user()->merchant->id)
            ->whereIn('status', [
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
            ->whereBetween('updated_at', [$date['from'], $date['to']])
            ->sum('cod_amount');
        //end failure delivered parcel


        //inside dhaka parcel
        $inside_dhaka_parcel_count               = Parcel::where('merchant_id', Auth::user()->merchant->id)
            ->whereIn('delivery_type_id', [DeliveryType::SAMEDAY, DeliveryType::NEXTDAY])
            ->whereBetween('updated_at', [$date['from'], $date['to']])
            ->count();
        $inside_dhaka_parcel_amount              = Parcel::where('merchant_id', Auth::user()->merchant->id)
            ->whereIn('delivery_type_id', [DeliveryType::SAMEDAY, DeliveryType::NEXTDAY])
            ->whereBetween('updated_at', [$date['from'], $date['to']])
            ->sum('cash_collection');
        $inside_dhaka_parcel_delivery_charge     = Parcel::where('merchant_id', Auth::user()->merchant->id)
            ->whereIn('delivery_type_id', [DeliveryType::SAMEDAY, DeliveryType::NEXTDAY])
            ->whereBetween('updated_at', [$date['from'], $date['to']])
            ->sum('total_delivery_amount');
        //end inside dhaka parcel

        //outside dhaka parcel
        $outside_dhaka_parcel_count               = Parcel::where('merchant_id', Auth::user()->merchant->id)
            ->whereIn('delivery_type_id', [DeliveryType::SUBCITY, DeliveryType::OUTSIDECITY])
            ->whereBetween('updated_at', [$date['from'], $date['to']])
            ->count();
        $outside_dhaka_parcel_amount              = Parcel::where('merchant_id', Auth::user()->merchant->id)
            ->whereIn('delivery_type_id', [DeliveryType::SUBCITY, DeliveryType::OUTSIDECITY])
            ->whereBetween('updated_at', [$date['from'], $date['to']])
            ->sum('cash_collection');
        $outside_dhaka_parcel_delivery_charge     = Parcel::where('merchant_id', Auth::user()->merchant->id)
            ->whereIn('delivery_type_id', [DeliveryType::SUBCITY, DeliveryType::OUTSIDECITY])
            ->whereBetween('updated_at', [$date['from'], $date['to']])
            ->sum('total_delivery_amount');
        //end outside dhaka parcel

        //last 24 hours parcel
        $nowDateTime           = Carbon::now()->format('Y-m-d H:i:s');
        $prev24HoursDateTime   = Carbon::parse(Carbon::now()->format('Y-m-d H:i:s'))->subHours(24)->format('Y-m-d H:i:s');
        $last24HoursDateTime   = $this->repo->FromTo($request);

        $last24HParcel_count             = Parcel::where('merchant_id', Auth::user()->merchant->id)
            ->whereBetween('updated_at', [$last24HoursDateTime['from'], $last24HoursDateTime['to']])
            ->count();
        $last24HParcel_amount            = Parcel::where('merchant_id', Auth::user()->merchant->id)
            ->whereBetween('updated_at', [$last24HoursDateTime['from'], $last24HoursDateTime['to']])
            ->sum('cash_collection');
        $last24HParcel_delivery_charge   = Parcel::where('merchant_id', Auth::user()->merchant->id)
            ->whereBetween('updated_at', [$last24HoursDateTime['from'], $last24HoursDateTime['to']])
            ->sum('total_delivery_amount');
        //end last 24 hours parcel

        //end date wise 

        //not filter wise it is all parcel info
        $merchant        = Merchant::find(Auth::user()->merchant->id);
        $t_parcel        = Parcel::where('merchant_id', Auth::user()->merchant->id)->count();
        $total_amount    = Parcel::where('merchant_id', Auth::user()->merchant->id)
            ->sum('total_delivery_amount');
        $t_return        = Parcel::where('status', ParcelStatus::RETURNED_MERCHANT)
            ->where('merchant_id', Auth::user()->merchant->id)
            ->count();
        $return_fees     = Parcel::where('status', ParcelStatus::RETURNED_MERCHANT)
            ->where('merchant_id', Auth::user()->merchant->id)
            ->sum('return_charges');
        $t_delivered     = Parcel::whereIn('status', [ParcelStatus::DELIVERED, ParcelStatus::PARTIAL_DELIVERED])
            ->where('merchant_id', Auth::user()->merchant->id)
            ->count();
        $delivered_amount = Parcel::whereIn('status', [ParcelStatus::DELIVERED, ParcelStatus::PARTIAL_DELIVERED])
            ->where('merchant_id', Auth::user()->merchant->id)
            ->sum('cash_collection');
        $t_delivery_fee  = Parcel::where('merchant_id', Auth::user()->merchant->id)
            ->whereIn('status', [ParcelStatus::DELIVERED, ParcelStatus::PARTIAL_DELIVERED])
            ->sum('total_delivery_amount');
        $t_cod_amount    = Parcel::where('merchant_id', Auth::user()->merchant->id)
            ->whereIn('status', [ParcelStatus::DELIVERED, ParcelStatus::PARTIAL_DELIVERED])
            ->sum('cod_amount');
        return view('backend.merchant_panel.statistics', compact(
            'request',
            't_pending',
            't_in_transit',
            'td_delivered',
            't_balance_pending',
            't_returned_merchant',
            'total_delivered_count',
            't_delivered_collected_amount',
            'delivered_delivery_charge',
            'delivered_cod',
            'total_par_delivered_count',
            't_par_delivered_collected_amount',
            'par_delivered_delivery_charge',
            'par_delivered_cod',
            'inside_dhaka_parcel_count',
            'inside_dhaka_parcel_amount',
            'inside_dhaka_parcel_delivery_charge',
            'outside_dhaka_parcel_count',
            'outside_dhaka_parcel_amount',
            'outside_dhaka_parcel_delivery_charge',
            'last24HParcel_count',
            'last24HParcel_amount',
            'last24HParcel_delivery_charge',
            'merchant',
            't_parcel',
            'total_amount',
            't_delivered',
            'delivered_amount',
            't_return',
            'return_fees',
            't_delivery_fee',
            't_cod_amount',
        ));
    }

    public function adminStatistics(Request $request)
    {
        $this->data['merchants']        = $this->merchantRepo->all();
        $this->data['request']          = $request;
        return view('backend.statistics.merchant_statistics', $this->data);
    }

    public function deliverymanStatistics(Request $request)
    {
        $this->data['deliverymans']     = $this->deliverymanRepo->all();
        $this->data['request']          = $request;
        return view('backend.statistics.deliveryman_statistics', $this->data);
    }

    public function analytics(Request $request)
    {
        $d      = $request->filter_date ?? Carbon::now()->format('m/d/Y') . ' To ' . Carbon::now()->format('m/d/Y');
        $date   = $this->repo->analyticsFromTo($d);
        $request['filter_date']  = $d;
        $data = [];
        $data['request'] = $request;
        $data['total_orders']             = $this->repo->analytics($request)
            ->where(function ($query) {
                if (request()->merchant_id) :
                    $query->where('merchant_id', request()->merchant_id);
                endif;
            })
            ->whereBetween('updated_at', [$date['from'], $date['to']])
            ->count();
        $data['total_delivered']          =  $this->repo->analytics($request)
            ->where(function ($query) {
                if (request()->merchant_id) :
                    $query->where('merchant_id', request()->merchant_id);
                endif;
            })
            ->whereIn('status', [ParcelStatus::DELIVERED, ParcelStatus::PARTIAL_DELIVERED])
            ->whereBetween('updated_at', [$date['from'], $date['to']])
            ->count();
        $data['total_cancelled']          =  $this->repo->analytics($request)
            ->where(function ($query) {
                if (request()->merchant_id) :
                    $query->where('merchant_id', request()->merchant_id);
                endif;
            })
            ->where('status', ParcelStatus::PARCEL_CANCEL)
            ->whereBetween('updated_at', [$date['from'], $date['to']])
            ->count();

        $data['total_distance']           = $this->repo->analytics($request)
            ->where(function ($query) {
                if (request()->merchant_id) :
                    $query->where('merchant_id', request()->merchant_id);
                endif;
            })
            ->whereBetween('updated_at', [$date['from'], $date['to']])
            ->sum('distance_km');

        $data['total_inside_distance']    = $this->repo->analytics($request)
            ->where(function ($query) {
                if (request()->merchant_id) :
                    $query->where('merchant_id', request()->merchant_id);
                endif;
            })
            ->where('delivery_type_id', DeliveryType::SAMEDAY)
            ->whereBetween('updated_at', [$date['from'], $date['to']])
            ->sum('distance_km');

        $data['total_outside_distance']   = $this->repo->analytics($request)
            ->where(function ($query) {
                if (request()->merchant_id) :
                    $query->where('merchant_id', request()->merchant_id);
                endif;
            })
            ->where('delivery_type_id', DeliveryType::SUBCITY)
            ->whereBetween('updated_at', [$date['from'], $date['to']])
            ->sum('distance_km');

        $data['total_expense']            = $this->repo->analytics($request)
            ->where(function ($query) {
                if (request()->merchant_id) :
                    $query->where('merchant_id', request()->merchant_id);
                endif;
            })
            ->whereBetween('updated_at', [$date['from'], $date['to']])
            ->sum('total_delivery_amount');

        $data['total_inside_expense']     = $this->repo->analytics($request)
            ->where(function ($query) {
                if (request()->merchant_id) :
                    $query->where('merchant_id', request()->merchant_id);
                endif;
            })
            ->where('delivery_type_id', DeliveryType::SAMEDAY)
            ->whereBetween('updated_at', [$date['from'], $date['to']])
            ->sum('total_delivery_amount');

        $data['total_outside_expense']    = $this->repo->analytics($request)
            ->where(function ($query) {
                if (request()->merchant_id) :
                    $query->where('merchant_id', request()->merchant_id);
                endif;
            })
            ->where('delivery_type_id', DeliveryType::SUBCITY)
            ->whereBetween('updated_at', [$date['from'], $date['to']])
            ->sum('total_delivery_amount');
        $data['merchants']  = Merchant::where('status', Status::ACTIVE)->get();


        $data['seven_days_order']     = $this->repo->seven_days_order_summery();
        $data['current_month_order']  = $this->repo->monthly_order_summery();

        return view('backend.analytics', $data);
    }
}
