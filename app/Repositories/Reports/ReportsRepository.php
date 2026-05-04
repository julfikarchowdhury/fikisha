<?php

namespace App\Repositories\Reports;

use App\Enums\AccountHeads;
use App\Enums\ParcelStatus;
use App\Models\Backend\BankTransaction;
use App\Models\Backend\DeliveryMan;
use App\Models\Backend\DeliverymanStatement;
use App\Models\Backend\Expense;
use App\Models\Backend\Income;
use App\Models\Backend\Parcel;
use App\Models\Backend\Payroll\SalaryGenerate;
use App\Models\Backend\Salary;
use App\Models\CashReceivedFromDeliveryman;
use App\Repositories\Reports\ReportsInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ReportsRepository implements ReportsInterface
{
    public function parcelReports($request)
    {
            $parcels =  Parcel::with('parcelEvent')
                ->orderBy('id', 'asc')
                ->where(function ($query) use ($request) {
                    if ($request->parcel_date) {
                        $date = explode('To', $request->parcel_date);
                        if (is_array($date)) {
                            $from   = Carbon::parse(trim($date[0]))->startOfDay()->toDateTimeString();
                            $to     = Carbon::parse(trim($date[1]))->endOfDay()->toDateTimeString();
                            $query->whereBetween('created_at', [$from, $to]);
                        }
                    }
                    if ($request->parcel_status) {
                        if ($request->parcel_status == ParcelStatus::PENDING) {
                            $query->whereIn('status', [
                                ParcelStatus::PENDING,
                                ParcelStatus::DELIVERY_RE_SCHEDULE,
                                ParcelStatus::DELIVERY_MAN_ASSIGN_CANCEL,
                                ParcelStatus::DELIVERY_RE_SCHEDULE_CANCEL,
                            ]);
                        } elseif ($request->parcel_status == ParcelStatus::DELIVERY_MAN_ASSIGN) {
                            $query->whereIn('status', [
                                ParcelStatus::DELIVERY_MAN_ASSIGN,
                                ParcelStatus::DELIVERY_RE_SCHEDULE,
                                ParcelStatus::CONFIRMED,
                                ParcelStatus::CONFIRMED_BOOKING,
                                ParcelStatus::UNCONFIRMED,
                                ParcelStatus::UNCONFIRMED_BOOKING,
                            ]);
                        } elseif ($request->parcel_status == ParcelStatus::PROCESSING) {
                            $query->whereIn('status', [
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
                            ]);
                        } elseif ($request->parcel_status == ParcelStatus::DELIVERED) {
                            $query->whereIn('status', [
                                ParcelStatus::DELIVERED,
                                ParcelStatus::PARTIAL_DELIVERED
                            ]);
                        } elseif ($request->parcel_status == ParcelStatus::DELIVERY_FAILURE) {
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
                                ParcelStatus::RETURN_RECEIVED_BY_MERCHANT,
                            ]);
                        } else {
                            $query->where('status', $request->parcel_status);
                        }
                    }
                    if ($request->parcel_merchant_id) {
                        $query->where(['merchant_id' => $request->parcel_merchant_id]);
                    }
                })->get();
            return $parcels;
    }

    public function merchantParcelReports($request)
    {
        return Parcel::with('parcelEvent')
            ->where('merchant_id', Auth::user()->merchant->id)
            ->where(function ($query) use ($request) {
                if ($request->parcel_date) {
                    $date = explode('To', $request->parcel_date);
                    if (is_array($date)) {
                        $from   = Carbon::parse(trim($date[0]))->startOfDay()->toDateTimeString();
                        $to     = Carbon::parse(trim($date[1]))->endOfDay()->toDateTimeString();
                        $query->whereBetween('created_at', [$from, $to]);
                    }
                }

                if ($request->parcel_status) {
                    if ($request->parcel_status == ParcelStatus::PENDING) {
                        $query->whereIn('status', [
                            ParcelStatus::PENDING,
                            ParcelStatus::DELIVERY_RE_SCHEDULE,
                            ParcelStatus::DELIVERY_MAN_ASSIGN_CANCEL,
                            ParcelStatus::DELIVERY_RE_SCHEDULE_CANCEL,
                        ]);
                    } elseif ($request->parcel_status == ParcelStatus::DELIVERY_MAN_ASSIGN) {
                        $query->whereIn('status', [
                            ParcelStatus::DELIVERY_MAN_ASSIGN,
                            ParcelStatus::DELIVERY_RE_SCHEDULE,
                            ParcelStatus::CONFIRMED,
                            ParcelStatus::CONFIRMED_BOOKING,
                            ParcelStatus::UNCONFIRMED,
                            ParcelStatus::UNCONFIRMED_BOOKING,
                        ]);
                    } elseif ($request->parcel_status == ParcelStatus::PROCESSING) {
                        $query->whereIn('status', [
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
                        ]);
                    } elseif ($request->parcel_status == ParcelStatus::DELIVERED) {
                        $query->whereIn('status', [
                            ParcelStatus::DELIVERED,
                            ParcelStatus::PARTIAL_DELIVERED
                        ]);
                    } elseif ($request->parcel_status == ParcelStatus::DELIVERY_FAILURE) {
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
                            ParcelStatus::RETURN_RECEIVED_BY_MERCHANT,
                        ]);
                    } else {
                        $query->where('status', $request->parcel_status);
                    }
                }

                if ($request->parcel_merchant_id) {
                    $query->where(['merchant_id' => $request->parcel_merchant_id]);
                }
            })
            ->orderBy('id', 'asc')
            ->get();
    }

    public function parcelWiseProfitReports($request)
    {
            return   Parcel::with('parcelEvent')
                ->whereIn('status', [
                    ParcelStatus::DELIVERED,
                    ParcelStatus::PARTIAL_DELIVERED,
                    ParcelStatus::MARKETPLACE_DELIVERED,
                ])
                ->orderBy('id', 'asc')
                ->where(function ($query) use ($request) {
                if ($request->parcel_date) {
                    $date = explode('To', $request->parcel_date);
                    if (is_array($date)) {
                        $from   = Carbon::parse(trim($date[0]))->startOfDay()->toDateTimeString();
                        $to     = Carbon::parse(trim($date[1]))->endOfDay()->toDateTimeString();
                        $query->whereBetween('updated_at', [$from, $to]);
                    }
                }

                if ($request->parcel_tracking_id) {
                    $query->whereIn('id', $request->parcel_tracking_id);
                }

                if ($request->parcel_merchant_id) {
                    $query->where(['merchant_id' => $request->parcel_merchant_id]);
                }
            })->get();
    }

    //salary reports
    public function salaryReports($request)
    {

        $salaryPayment  = Salary::with('user')->where(function ($query) use ($request) {
            if ($request->salary_date) {
                $date = explode('To', $request->salary_date);
                if (is_array($date)) {
                    $from   = Carbon::parse(trim($date[0]))->startOfDay()->toDateTimeString();
                    $to     = Carbon::parse(trim($date[1]))->endOfDay()->toDateTimeString();
                    $query->whereBetween('created_at', [$from, $to]);
                }
            }
            if ($request->month) {
                $query->where('month', $request->month);
            }

            if ($request->user_id) :
                $query->whereIn('user_id', $request->user_id);
            endif;
        })->orderBy('month', 'asc')->get();
        $salary         = SalaryGenerate::with('user')->where(function ($query) use ($request) {
            if ($request->salary_date) {
                $date = explode('To', $request->salary_date);
                if (is_array($date)) {
                    $from   = Carbon::parse(trim($date[0]))->startOfDay()->toDateTimeString();
                    $to     = Carbon::parse(trim($date[1]))->endOfDay()->toDateTimeString();
                    $query->whereBetween('created_at', [$from, $to]);
                }
            }
            if ($request->month) {
                $query->where('month', $request->month);
            }
            if ($request->user_id) :
                $query->whereIn('user_id', $request->user_id);
            endif;
        })->orderBy('month', 'asc')->get();

        $data['salaryPayment']   = $salaryPayment->groupBy('month');
        $data['salary']          = $salary->groupBy('month');
        return $data;
    }

    public function salaryReportsPrint($request)
    {
        $data = [];
        $data['salaries'] = Salary::whereIn('id', $request->salary_ids)->get()->groupBy('month');
        return $data;
    }
    public function MerchantExport($request)
    {
        //
    }
    //merchant reports
    public function parcelTotalSummeryReports($request)
    {

        $parcels =   Parcel::with('parcelEvent')->where(function ($query) use ($request) {
            if ($request->parcel_date) {
                $date = explode('To', $request->parcel_date);
                if (is_array($date)) {
                    $from   = Carbon::parse(trim($date[0]))->startOfDay()->toDateTimeString();
                    $to     = Carbon::parse(trim($date[1]))->endOfDay()->toDateTimeString();

                    $query->whereBetween('created_at', [$from, $to]);
                }
            }

            if ($request->parcel_merchant_id) {
                $query->where(['merchant_id' => $request->parcel_merchant_id]);
            }
            if ($request->merchant_id) {
                $query->where(['merchant_id' => $request->merchant_id]);
            }
        })->orderBy('id', 'asc')->get();
        return $parcels;
    }

    public function commissionDeliveryman($request)
    {

        $commissionDeliveryMan  =   Expense::where('account_head_id', 5)->where(function ($query) use ($request) {
            if ($request->parcel_date) {
                $date = explode('To', $request->parcel_date);
                if (is_array($date)) {
                    $from   = Carbon::parse(trim($date[0]))->startOfDay()->toDateTimeString();
                    $to     = Carbon::parse(trim($date[1]))->endOfDay()->toDateTimeString();

                    $query->whereBetween('date', [$from, $to]);
                }
            }

            if ($request->delivery_man_id) {
                $query->where(['delivery_man_id' => $request->delivery_man_id]);
            }
        })->orderBy('id', 'asc')->get();
        return $commissionDeliveryMan;
    }


    public function cashReceivedDeliveryman($request)
    {

        $cashReceivedDeliveryMan  =   Income::where('account_head_id', 2)->where(function ($query) use ($request) {

            if ($request->parcel_date) {
                $date = explode('To', $request->parcel_date);
                if (is_array($date)) {
                    $from   = Carbon::parse(trim($date[0]))->startOfDay()->toDateTimeString();
                    $to     = Carbon::parse(trim($date[1]))->endOfDay()->toDateTimeString();
                    $query->whereBetween('date', [$from, $to]);
                }
            }
            if ($request->delivery_man_id) {
                $query->where(['delivery_man_id' => $request->delivery_man_id]);
            }
        })->orderBy('id', 'asc')->get();

        return $cashReceivedDeliveryMan;
    }


    public function incomeExpense($type)
    {
        return BankTransaction::where('type', $type)->orderByDesc('id')->sum('amount');
    }
    //delivery man reports
    public function deliverymanreportParcels($request)
    {

        $parcels_id   = $this->deliverymanStatement($request)->pluck('parcel_id');
        $ids = [];
        foreach ($parcels_id as $id) {
            $ids[] = $id;
        }
        $parcels =   Parcel::whereIn('id', $ids)->whereIn('status', [parcelStatus::DELIVERED, parcelStatus::PARTIAL_DELIVERED, parcelStatus::RECEIVED_WAREHOUSE, parcelStatus::RETURN_ASSIGN_TO_MERCHANT])->where(function ($query) use ($request) {
            if ($request->parcel_date) {
                $date = explode('To', $request->parcel_date);
                if (is_array($date)) {
                    $from   = Carbon::parse(trim($date[0]))->startOfDay()->toDateTimeString();
                    $to     = Carbon::parse(trim($date[1]))->endOfDay()->toDateTimeString();
                    $query->whereBetween('created_at', [$from, $to]);
                }
            }
        })->orderBy('id', 'asc')->get();
        return $parcels;
    }
    public function deliverymanStatement($request)
    {
        $deliveryStatement =   DeliverymanStatement::where(function ($query) use ($request) {
            if ($request->parcel_date) {
                $date = explode('To', $request->parcel_date);
                if (is_array($date)) {
                    $from   = Carbon::parse(trim($date[0]))->startOfDay()->toDateTimeString();
                    $to     = Carbon::parse(trim($date[1]))->endOfDay()->toDateTimeString();
                    $query->whereBetween('created_at', [$from, $to]);
                }
            }

            if ($request->delivery_man_id) {
                $query->where(['delivery_man_id' => $request->delivery_man_id]);
            }
        })->orderBy('id', 'asc')->get();
        return $deliveryStatement;
    }
}
