<?php

namespace App\Repositories\Dashboard;

use App\Enums\AccountHeads;
use App\Enums\InvoiceStatus;
use App\Enums\ParcelStatus;
use App\Enums\Status;
use App\Models\Backend\Account;
use App\Models\Backend\BankTransaction;
use App\Models\Backend\CourierStatement;
use App\Models\Backend\DeliverymanStatement;
use App\Models\Backend\Expense;
use App\Models\Backend\Income;
use App\Models\Backend\Merchantpanel\Invoice;
use App\Models\Backend\MerchantStatement;
use App\Models\Backend\Parcel;
use App\Models\Backend\Payroll\SalaryGenerate;
use App\Models\Backend\Salary;
use App\Repositories\Dashboard\DashboardInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardRepository implements DashboardInterface
{

    public function FromTo($request)
    {
        if ($request->days     == 'today') :
            $startDate                      = Carbon::today()->format('Y-m-d'); //today date
            $endDate                        = Carbon::today()->format('Y-m-d'); // today date
            $subDays                        = Carbon::parse(trim($startDate))->startOfDay()->toDateTimeString(); //from
            $todayDate                      = Carbon::parse(trim($endDate))->endOfDay()->toDateTimeString(); //to
        elseif ($request->days     == 'week') :
            $subDays                        = Carbon::parse(Carbon::today()->subDays(7)->format('Y-m-d'))->startOfDay()->toDateTimeString(); // from today to 7 days previus date
            $todayDate                      = Carbon::parse(Carbon::today()->format('Y-m-d'))->endOfDay()->toDateTimeString(); //to today date
        elseif ($request->days     == '15days') :
            $subDays                        = Carbon::parse(Carbon::today()->subDays(15)->format('Y-m-d'))->startOfDay()->toDateTimeString(); //from today to 15days previus date
            $todayDate                      = Carbon::parse(Carbon::today()->format('Y-m-d'))->endOfDay()->toDateTimeString(); //to today date
        elseif ($request->days == 'month') :
            $subDays                        = Carbon::parse(Carbon::today()->subDays(30)->format('Y-m-d'))->startOfDay()->toDateTimeString(); //from  today to 30 days previus date
            $todayDate                      = Carbon::parse(Carbon::today()->format('Y-m-d'))->endOfDay()->toDateTimeString(); //to today date
        elseif ($request->days == 'yesterday') :
            $subDays                        = Carbon::parse(Carbon::today()->subDays(1)->format('Y-m-d'))->startOfDay()->toDateTimeString(); // yesterday
            $todayDate                      = Carbon::parse(Carbon::today()->subDays(1)->format('Y-m-d'))->endOfDay()->toDateTimeString(); // yesterday
        elseif ($request->days == 'custom' && !empty($request->filter_date)) :
            $date = explode('To', $request->filter_date);
            if (is_array($date)) {
                $from   = Carbon::parse(trim($date[0]))->startOfDay()->toDateTimeString();
                $to     = Carbon::parse(trim($date[1]))->endOfDay()->toDateTimeString();
            }
            $subDays   = $from;
            $todayDate = $to;
        else :
            $subDays                        = Carbon::parse('1971-01-01')->startOfDay()->toDateTimeString(); //start time today to 7 days previus date
            $todayDate                      = Carbon::parse(Carbon::today()->addDays(1)->format('Y-m-d'))->endOfDay()->toDateTimeString(); // end time today date
        endif;
        $data = [];
        $data['from']                   = $subDays;
        $data['to']                     = $todayDate;
        return $data;
    }

    public function Dates($request)
    {
        if ($request->days     == 'today') :
            $startDate                      = Carbon::today()->format('Y-m-d'); //today date
            $endDate                        = Carbon::today()->format('Y-m-d'); // today date
            $subDays                        = Carbon::parse(trim($startDate))->startOfDay()->toDateTimeString();
            $todayDate                      = Carbon::parse(trim($endDate))->endOfDay()->toDateTimeString();

        elseif ($request->days     == 'week') :
            $todayDate                      = Carbon::today()->addDays(1)->format('Y-m-d'); //today date
            $subDays                        = Carbon::today()->subDays(7)->format('Y-m-d'); // today to 7 days previus date

        elseif ($request->days     == '15days') :
            $todayDate                      = Carbon::today()->addDays(1)->format('Y-m-d'); //today date
            $subDays                        = Carbon::today()->subDays(15)->format('Y-m-d'); // today to 15days previus date
        elseif ($request->days == 'month') :
            $todayDate                      = Carbon::today()->addDays(1)->format('Y-m-d'); //today date
            $subDays                        = Carbon::today()->subDays(30)->format('Y-m-d'); // today to 30 days previus date
        elseif ($request->days == 'yesterday') :
            $todayDate                      = Carbon::today()->format('Y-m-d'); //yesterday date
            $subDays                        = Carbon::today()->subDays(1)->format('Y-m-d'); // yesterday
        elseif ($request->days == 'custom') :
            $date = explode('To', $request->filter_date);
            if (is_array($date)) {
                $from   = Carbon::parse(trim($date[0]))->startOfDay()->toDateTimeString();
                $to     = Carbon::parse(trim($date[1]))->endOfDay()->toDateTimeString();
            }
            $subDays   = $from;
            $todayDate = $to;
        else :
            $todayDate                      = Carbon::today()->format('Y-m-d'); //today date
            $subDays                        = Carbon::today()->subDays(7)->format('Y-m-d'); // today to 7 days previus date
        endif;
        $totaldays                         = Carbon::parse($subDays)->diffInDays($todayDate); //today date to previus date total days
        $d = $subDays;
        $dates = [];
        for ($i = 0; $i < $totaldays; $i++) {
            $dates[] = Carbon::parse($d)->addHours(24)->format('d-m-Y');
            $d       = Carbon::parse($d)->addHours(24)->format('d-m-Y');
        }

        return $dates;
    }

    public function incomeDate($request)
    {
        //
    }
    public function expenseDate($request)
    {
        //
    }

    public function parcelPosition($request, $status, $date)
    {
        return Parcel::where(function ($query) use ($request, $status) {
            if ($status == ParcelStatus::DELIVERED) {
                $query->whereIn('status', [
                    ParcelStatus::DELIVERED,
                    ParcelStatus::PARTIAL_DELIVERED
                ]);
            } elseif ($status == ParcelStatus::DELIVERY_FAILURE) {
                $query->whereIn('status', [
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
                    ParcelStatus::RETURN_RECEIVED_BY_MERCHANT
                ]);
            } else {
                $query->where('status', $status);
            }
        })->whereBetween('updated_at', [$date['from'], $date['to']])->get();
    }

    public function parcelPositionDeliveryFailure($request, $date)
    {
        return Parcel::query()
            ->where(function ($query) use ($request) {
                if (request()->merchant_id) :
                    $query->where('merchant_id', request()->merchant_id);
                endif;
                $query->whereIn('status', [
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
                    ParcelStatus::RETURN_RECEIVED_BY_MERCHANT
                ]);
            })
            ->whereBetween('updated_at', [$date['from'], $date['to']])
            ->get();
    }

    public function recentAccounts($request, $date)
    {
        return Account::where('status', Status::ACTIVE)->whereBetween('updated_at', [$date['from'], $date['to']])->orderBy('id', 'desc')->limit(3)->get(); //recent accounts
    }

    public function salaryGenerated($date)
    {


        return SalaryGenerate::whereBetween('updated_at', [$date['from'], $date['to']])->get();
    }
    public function salary($date)
    {
        return Salary::whereBetween('updated_at', [$date['from'], $date['to']])->get();
    }

    public function salaries($date)
    {
        return Salary::whereBetween('updated_at', [$date['from'], $date['to']]);
    }


    public function bankTransaction($date, $request = null)
    {
        return BankTransaction::where(function ($query) use ($request, $date) {
            $query->whereBetween('date', [$date['from'], $date['to']]);
        })->whereBetween('updated_at', [$date['from'], $date['to']])->orderByDesc('id')->limit(5)->get();
    }

    // Total income
    public function income($date, $request = null)
    {
        return Income::where(function ($query) use ($request, $date) {
            $query->whereHas('parcel', function ($query) {
                if (request()->province_id) :
                    $query->where('from_state_id', request()->province_id);
                endif;
                if (request()->merchant_id) :
                    $query->where('merchant_id', request()->merchant_id);
                endif;
            });
            $query->whereBetween('date', [$date['from'], $date['to']]);
        })->sum('amount');
    }

    // Total Expense
    public function expense($date, $request = null)
    {
        return Expense::where(function ($query) use ($request, $date) {
            $query->whereHas('parcel', function ($query) {
                if (request()->province_id) :
                    $query->where('from_state_id', request()->province_id);
                endif;
                if (request()->merchant_id) :
                    $query->where('merchant_id', request()->merchant_id);
                endif;
            });
            $query->whereBetween('date', [$date['from'], $date['to']]);
        })->sum('amount');
    }

    //merchant total income
    public function merchantIncome($date, $request = null)
    {
        return MerchantStatement::where(function ($query) use ($request, $date) {
            $query->whereHas('parcel', function ($query) use ($request) {
                if ($request && $request->province_id) :
                    $query->where('from_state_id', $request->province_id);
                endif;
                if ($request && $request->merchant_id) :
                    $query->where('merchant_id', $request->merchant_id);
                endif;
            });
            $query->whereBetween('date', [$date['from'], $date['to']]);
        })->where('type', AccountHeads::INCOME)->sum('amount');
    }

    //merchant total expense
    public function merchantExpense($date, $request = null)
    {
        return  MerchantStatement::where(function ($query) use ($request, $date) {
            $query->whereHas('parcel', function ($query) use ($request) {
                if ($request && $request->province_id) :
                    $query->where('from_state_id', $request->province_id);
                endif;
                if ($request && $request->merchant_id) :
                    $query->where('merchant_id', $request->merchant_id);
                endif;
            });
            $query->whereBetween('date', [$date['from'], $date['to']]);
        })->where('type', AccountHeads::EXPENSE)->sum('amount');
    }

    //Deliveryman total income
    public function deliverymanIncome($date, $request = null)
    {

        return DeliverymanStatement::where(function ($query) use ($request, $date) {
            $query->whereHas('parcel', function ($query) use ($request) {
                if ($request && $request->province_id) :
                    $query->where('from_state_id', $request->province_id);
                endif;
                if ($request && $request->merchant_id) :
                    $query->where('merchant_id', $request->merchant_id);
                endif;
            });
            $query->whereBetween('date', [$date['from'], $date['to']]);
        })->where('type', AccountHeads::INCOME)->sum('amount');
    }

    //Deliveryman total expense
    public function deliverymanExpense($date, $request = null)
    {
        return DeliverymanStatement::where(function ($query) use ($request, $date) {
            $query->whereHas('parcel', function ($query) use ($request) {
                if ($request && $request->province_id) :
                    $query->where('from_state_id', $request->province_id);
                endif;
                if ($request && $request->merchant_id) :
                    $query->where('merchant_id', $request->merchant_id);
                endif;
            });
            $query->whereBetween('date', [$date['from'], $date['to']]);
        })->where('type', AccountHeads::EXPENSE)->sum('amount');
    }

    //Courier total income
    public function courierIncome($date, $request = null)
    {
        return CourierStatement::where(function ($query) use ($request, $date) {
            $query->whereHas('parcel', function ($query) use ($request) {
                if ($request && $request->province_id) :
                    $query->where('from_state_id', $request->province_id);
                endif;
                if ($request && $request->merchant_id) :
                    $query->where('merchant_id', $request->merchant_id);
                endif;
            });
            $query->whereBetween('date', [$date['from'], $date['to']]);
        })->where('type', AccountHeads::INCOME)->sum('amount');
    }

    //Courier total Expense
    public function courierExpense($date, $request = null)
    {
        return CourierStatement::where(function ($query) use ($request, $date) {
            $query->whereHas('parcel', function ($query) use ($request) {
                if ($request && $request->province_id) :
                    $query->where('from_state_id', $request->province_id);
                endif;
                if ($request && $request->merchant_id) :
                    $query->where('merchant_id', $request->merchant_id);
                endif;
            });
            $query->whereBetween('date', [$date['from'], $date['to']]);
        })->where('type', AccountHeads::EXPENSE)->sum('amount');
    }

    //dashbord balance
    public function balanceDetails()
    {
        $data = [];

        $parcel_ids   = [];
        $invoices = Invoice::where('merchant_id', Auth::user()->merchant->id)->where('status', InvoiceStatus::PAID)->pluck('parcels_id');
        foreach ($invoices as $invoice) :
            if (!blank($invoice)) :
                $parcel_ids = array_merge($parcel_ids, $invoice);
            endif;
        endforeach;
        $parcels =  Parcel::where('merchant_id', Auth::user()->merchant->id)->whereNotIn('id', $parcel_ids)->whereIn('status', [ParcelStatus::PARTIAL_DELIVERED, ParcelStatus::DELIVERED]);
        $data['amount_delivered']           = $parcels->sum('cash_collection');
        $data['payable_delivery_charge']    = $parcels->sum('delivery_charge');
        $data['sub_total']                  = $data['amount_delivered'] - $data['payable_delivery_charge'];
        $data['vat_amount']                 = $parcels->sum('vat_amount');
        $data['cod_charge']                 = $parcels->sum('cod_amount');
        $data['available_balance']          = ($data['sub_total'] - $data['vat_amount']) - $data['cod_charge'];
        $data['clearable_parcels']          = $parcels->count();
        return $data;
    }
    public function availableParcels()
    {
        $parcel_ids   = [];
        $invoices = Invoice::where('merchant_id', Auth::user()->merchant->id)->where('status', InvoiceStatus::PAID)->pluck('parcels_id');
        foreach ($invoices as $invoice) :
            if (!blank($invoice)) :
                $parcel_ids = array_merge($parcel_ids, $invoice);
            endif;
        endforeach;
        $parcels =  Parcel::where('merchant_id', Auth::user()->merchant->id)->whereNotIn('id', $parcel_ids)->whereIn('status', [ParcelStatus::PARTIAL_DELIVERED, ParcelStatus::DELIVERED])->get();
        return $parcels;
    }



    public function analyticsFromTo($date)
    {
        $date = explode('To', $date);
        $data = [];
        $data['from']   = '';
        $data['to']     = '';
        if (is_array($date)) {
            $data['from']   = Carbon::parse(trim($date[0]))->startOfDay()->toDateTimeString();
            $data['to']     = Carbon::parse(trim($date[1]))->endOfDay()->addSecond(1)->toDateTimeString();
        }

        return $data;
    }


    public function analytics($request)
    {
        return Parcel::query();
    }


    public function seven_days_order_summery()
    {

        $s_day           = Carbon::now()->subDays(6)->format('Y-m-d');
        $totaldays       = Carbon::parse($s_day)->diffInDays(Carbon::now()->addDay(1)->format('Y-m-d')); //today date to previus date total days

        $data['all_order']      = [];
        $data['pending_order']  = [];
        $data['complete_order'] = [];
        $data['cancel_order']   = [];
        for ($i = 0; $i < $totaldays; $i++) {
            $date          = Carbon::parse($s_day)->addDay($i)->format('d-m-Y');
            $start_date    = Carbon::parse($date)->startOfDay()->toDateTimeString();
            $end_date      = Carbon::parse($date)->endOfDay()->toDateTimeString();
            $data['all_order'][Carbon::parse($date)->format('d-M')]       = Parcel::whereBetween('created_at', [$start_date, $end_date])->count();
            $data['pending_order'][Carbon::parse($date)->format('d-M')]   = Parcel::where('status', ParcelStatus::PENDING)->whereBetween('created_at', [$start_date, $end_date])->count();
            $data['complete_order'][Carbon::parse($date)->format('d-M')]  = Parcel::whereIn('status', [ParcelStatus::DELIVERED, ParcelStatus::PARTIAL_DELIVERED])->whereBetween('created_at', [$start_date, $end_date])->count();
            $data['cancel_order'][Carbon::parse($date)->format('d-M')]    = Parcel::where('status', ParcelStatus::PARCEL_CANCEL)->whereBetween('created_at', [$start_date, $end_date])->count();
        }

        return $data;
    }

    public function monthly_order_summery()
    {

        $s_day           = Carbon::now()->startOfMonth()->format('Y-m-d');
        $totaldays       = Carbon::now()->daysInMonth; //today date to previus date total days
        $data['all_order']      = [];
        $data['pending_order']  = [];
        $data['complete_order'] = [];
        $data['cancel_order']   = [];
        for ($i = 0; $i < $totaldays; $i++) {
            $date          = Carbon::parse($s_day)->addDay($i)->format('d-m-Y');
            $start_date    = Carbon::parse($date)->startOfDay()->toDateTimeString();
            $end_date      = Carbon::parse($date)->endOfDay()->toDateTimeString();
            $data['all_order'][Carbon::parse($date)->format('d-M')]       =  Parcel::whereBetween('created_at', [$start_date, $end_date])->count();
            $data['pending_order'][Carbon::parse($date)->format('d-M')]   =  Parcel::where('status', ParcelStatus::PENDING)->whereBetween('created_at', [$start_date, $end_date])->count();
            $data['complete_order'][Carbon::parse($date)->format('d-M')]  =  Parcel::whereIn('status', [ParcelStatus::DELIVERED, ParcelStatus::PARTIAL_DELIVERED])->whereBetween('created_at', [$start_date, $end_date])->count();
            $data['cancel_order'][Carbon::parse($date)->format('d-M')]    = Parcel::where('status', ParcelStatus::PARCEL_CANCEL)->whereBetween('created_at', [$start_date, $end_date])->count();
        }
        return $data;
    }


    //merchant panel
    public function merchant_seven_days_order_summery()
    {

        $s_day           = Carbon::now()->subDays(6)->format('Y-m-d');
        $totaldays       = Carbon::parse($s_day)->diffInDays(Carbon::now()->addDay(1)->format('Y-m-d')); //today date to previus date total days

        $data['all_order']      = [];
        $data['pending_order']  = [];
        $data['complete_order'] = [];
        $data['cancel_order']   = [];
        for ($i = 0; $i < $totaldays; $i++) {
            $date          = Carbon::parse($s_day)->addDay($i)->format('d-m-Y');
            $start_date    = Carbon::parse($date)->startOfDay()->toDateTimeString();
            $end_date      = Carbon::parse($date)->endOfDay()->toDateTimeString();
            $data['all_order'][Carbon::parse($date)->format('d-M')]       = Parcel::where('merchant_id', Auth::user()->merchant->id)->whereBetween('created_at', [$start_date, $end_date])->count();
            $data['pending_order'][Carbon::parse($date)->format('d-M')]   = Parcel::where('merchant_id', Auth::user()->merchant->id)->where('status', ParcelStatus::PENDING)->whereBetween('created_at', [$start_date, $end_date])->count();
            $data['complete_order'][Carbon::parse($date)->format('d-M')]  = Parcel::where('merchant_id', Auth::user()->merchant->id)->whereIn('status', [ParcelStatus::DELIVERED, ParcelStatus::PARTIAL_DELIVERED])->whereBetween('created_at', [$start_date, $end_date])->count();
            $data['cancel_order'][Carbon::parse($date)->format('d-M')]    = Parcel::where('merchant_id', Auth::user()->merchant->id)->where('status', ParcelStatus::PARCEL_CANCEL)->whereBetween('created_at', [$start_date, $end_date])->count();
        }

        return $data;
    }

    public function merchant_monthly_order_summery()
    {

        $s_day           = Carbon::now()->startOfMonth()->format('Y-m-d');
        $totaldays       = Carbon::now()->daysInMonth; //today date to previus date total days
        $data['all_order']      = [];
        $data['pending_order']  = [];
        $data['complete_order'] = [];
        $data['cancel_order']   = [];
        for ($i = 0; $i < $totaldays; $i++) {
            $date          = Carbon::parse($s_day)->addDay($i)->format('d-m-Y');
            $start_date    = Carbon::parse($date)->startOfDay()->toDateTimeString();
            $end_date      = Carbon::parse($date)->endOfDay()->toDateTimeString();
            $data['all_order'][Carbon::parse($date)->format('d-M')]       =  Parcel::where('merchant_id', Auth::user()->merchant->id)->whereBetween('created_at', [$start_date, $end_date])->count();
            $data['pending_order'][Carbon::parse($date)->format('d-M')]   =  Parcel::where('merchant_id', Auth::user()->merchant->id)->where('status', ParcelStatus::PENDING)->whereBetween('created_at', [$start_date, $end_date])->count();
            $data['complete_order'][Carbon::parse($date)->format('d-M')]  =  Parcel::where('merchant_id', Auth::user()->merchant->id)->whereIn('status', [ParcelStatus::DELIVERED, ParcelStatus::PARTIAL_DELIVERED])->whereBetween('created_at', [$start_date, $end_date])->count();
            $data['cancel_order'][Carbon::parse($date)->format('d-M')]    = Parcel::where('merchant_id', Auth::user()->merchant->id)->where('status', ParcelStatus::PARCEL_CANCEL)->whereBetween('created_at', [$start_date, $end_date])->count();
        }
        return $data;
    }
}
