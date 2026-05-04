<?php

namespace App\Http\Controllers\Backend;

use App\Exports\ParcelStatusReports;
use App\Exports\ReportExports;
use App\Http\Controllers\Controller;
use App\Models\Backend\Parcel;
use App\Repositories\DeliveryMan\DeliveryManInterface;
use App\Repositories\BankTransaction\BankTransactionInterface;
use App\Repositories\Merchant\MerchantInterface;
use Illuminate\Http\Request;
use App\Repositories\Reports\ReportsInterface;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MerchantReports;
use App\Enums\ParcelStatus;
use App\Enums\UserType;
use App\Enums\RiderStatus;
use App\Models\Backend\RiderWallet;
use App\Models\Backend\PlatformLedgerTransaction;
use App\Models\User;

class ReportsController extends Controller
{
    protected $repo;
    protected $merchant;
    protected $deliveryman;
    protected $bankTransaction;
    public function __construct(ReportsInterface $repo, MerchantInterface $merchant, BankTransactionInterface $bankTransaction, DeliveryManInterface $deliveryman)
    {
        $this->repo         =  $repo;
        $this->merchant     =  $merchant;
        $this->deliveryman  =  $deliveryman;
        $this->bankTransaction     =  $bankTransaction;
    }
    public function parcelReports(Request $request)
    {
        $parcels = [];
        $hubs = $this->merchant->all_city();
        return view('backend.reports.parcel_reports', compact('request', 'parcels', 'hubs'));
    }

    public function parcelSReports(Request $request)
    {
        if ($this->repo->parcelReports($request)) {
            $parcels      =  $this->repo->parcelReports($request);
            $print        =   true;
            $parcel_ids   = '';
            foreach ($parcels as $key => $parcel) {
                $parcel_ids  = $parcel->id . ',' . $parcel_ids;
            }
            $hubs = $this->merchant->all_city();
            return view('backend.reports.parcel_reports', compact('parcels', 'request', 'print', 'parcel_ids', 'hubs'));
        } else {
            return redirect()->back();
        }
    }

    public function parcelReportsPrint(Request $request, $array)
    {

        $parcel_ids  = [];
        foreach (explode(',', $array) as  $id) {
            if ($id !== "") :
                $parcel_ids[] = $id;
            endif;
        }
        $parcels    = Parcel::whereIn('id', $parcel_ids)->orderBy('id')->get();
        return view('backend.reports.parcel_reports_print', compact('parcels'));
    }

    public function marketplaceEarnings(Request $request)
    {
        $query = Parcel::query()
            ->leftJoin('users', 'users.id', '=', 'parcels.delivery_man_id')
            ->where('parcels.status', ParcelStatus::MARKETPLACE_DELIVERED)
            ->select('parcels.*', 'users.name as rider_name');

        if ($request->filled('rider_id')) {
            $query->where('parcels.delivery_man_id', (int) $request->rider_id);
        }

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('parcels.updated_at', [$request->from_date, $request->to_date]);
        }

        $parcels = $query->orderByDesc('parcels.updated_at')->paginate(20);

        $summaryBase = Parcel::query()->where('status', ParcelStatus::MARKETPLACE_DELIVERED);
        if ($request->filled('rider_id')) {
            $summaryBase->where('delivery_man_id', (int) $request->rider_id);
        }
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $summaryBase->whereBetween('updated_at', [$request->from_date, $request->to_date]);
        }

        $grossPlatform = (float) (clone $summaryBase)->sum('platform_total_earning');

        $ledgerBase = PlatformLedgerTransaction::query()->where('direction', 'debit');
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $ledgerBase->whereBetween('created_at', [$request->from_date, $request->to_date]);
        }
        $platformRefunds = (float) (clone $ledgerBase)->sum('amount');
        $platformNet = round($grossPlatform - $platformRefunds, 2);

        $summaryQuery = Parcel::query()
            ->leftJoin('users', 'users.id', '=', 'parcels.delivery_man_id')
            ->where('parcels.status', ParcelStatus::MARKETPLACE_DELIVERED)
            ->selectRaw('parcels.delivery_man_id as rider_id, users.name as rider_name, COUNT(*) as total_deliveries, SUM(parcels.rider_earning) as total_earned, SUM(parcels.commission_amount) as total_commission, SUM(parcels.platform_total_earning) as total_platform_earning')
            ->groupBy('parcels.delivery_man_id', 'users.name');

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $summaryQuery->whereBetween('parcels.updated_at', [$request->from_date, $request->to_date]);
        }

        $riderSummaries = $summaryQuery->orderByDesc('total_deliveries')->get();
        $riders = User::where('user_type', UserType::DELIVERYMAN)->orderBy('name')->get();

        return view('backend.reports.marketplace_earnings', compact(
            'parcels',
            'riderSummaries',
            'riders',
            'request',
            'grossPlatform',
            'platformRefunds',
            'platformNet'
        ));
    }

    public function riderEarnings(Request $request)
    {
        $baseQuery = Parcel::query()
            ->leftJoin('users', 'users.id', '=', 'parcels.delivery_man_id')
            ->where('parcels.status', ParcelStatus::MARKETPLACE_DELIVERED)
            ->select('parcels.*', 'users.name as rider_name');

        if ($request->filled('rider_id')) {
            $baseQuery->where('parcels.delivery_man_id', (int) $request->rider_id);
        }

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $baseQuery->whereBetween('parcels.updated_at', [$request->from_date, $request->to_date]);
        }

        $summary = (clone $baseQuery)
            ->selectRaw('COUNT(*) as total_deliveries, SUM(parcels.base_delivery_charge) as total_base_charge, SUM(parcels.commission_amount) as total_commission, SUM(parcels.rider_earning) as total_rider_earning, SUM(parcels.platform_total_earning) as total_platform_earning')
            ->first();

        $parcels = $baseQuery->orderByDesc('parcels.updated_at')->paginate(20);
        $riders = User::where('user_type', UserType::DELIVERYMAN)->orderBy('name')->get();

        return view('backend.reports.rider_earnings', compact('parcels', 'summary', 'riders', 'request'));
    }

    public function riderOverview(Request $request)
    {
        $deliveredStatus = ParcelStatus::MARKETPLACE_DELIVERED;
        $fromDate = $request->from_date;
        $toDate = $request->to_date;

        $riderBase = User::where('user_type', UserType::DELIVERYMAN);
        if ($request->filled('rider_id')) {
            $riderBase->where('id', (int) $request->rider_id);
        }

        $parcelBase = Parcel::where('status', $deliveredStatus);
        if ($request->filled('rider_id')) {
            $parcelBase->where('delivery_man_id', (int) $request->rider_id);
        }
        if ($fromDate && $toDate) {
            $parcelBase->whereBetween('updated_at', [$fromDate, $toDate]);
        }

        $walletBase = RiderWallet::query();
        if ($request->filled('rider_id')) {
            $walletBase->where('rider_id', (int) $request->rider_id);
        }

        $summary = [
            'total_riders' => (clone $riderBase)->count(),
            'total_deliveries' => (clone $parcelBase)->count(),
            'total_earnings' => (float) (clone $parcelBase)->sum('rider_earning'),
            'total_wallet_balance' => (float) (clone $walletBase)->sum('balance'),
            'total_pending_withdraw' => (float) (clone $walletBase)->sum('pending_withdraw_amount'),
        ];

        $riders = User::query()
            ->where('users.user_type', UserType::DELIVERYMAN)
            ->leftJoin('delivery_man', 'delivery_man.user_id', '=', 'users.id')
            ->leftJoin('rider_wallets', 'rider_wallets.rider_id', '=', 'users.id')
            ->select([
                'users.id',
                'users.name',
                'users.mobile',
                'users.email',
                'delivery_man.vehicle_type',
                'delivery_man.rider_status',
                'delivery_man.is_available',
                'rider_wallets.balance',
                'rider_wallets.pending_withdraw_amount',
            ])
            ->selectRaw(
                '(select count(*) from parcels where parcels.delivery_man_id = users.id and parcels.status = ?' .
                ($fromDate && $toDate ? ' and parcels.updated_at between ? and ?' : '') .
                ') as total_deliveries',
                $fromDate && $toDate ? [$deliveredStatus, $fromDate, $toDate] : [$deliveredStatus]
            )
            ->selectRaw(
                '(select coalesce(sum(parcels.rider_earning),0) from parcels where parcels.delivery_man_id = users.id and parcels.status = ?' .
                ($fromDate && $toDate ? ' and parcels.updated_at between ? and ?' : '') .
                ') as total_earnings',
                $fromDate && $toDate ? [$deliveredStatus, $fromDate, $toDate] : [$deliveredStatus]
            )
            ->when($request->filled('rider_id'), function ($query) use ($request) {
                $query->where('users.id', (int) $request->rider_id);
            })
            ->orderBy('users.name')
            ->paginate(20);

        $statusLabels = RiderStatus::LABELS;
        $allRiders = User::where('user_type', UserType::DELIVERYMAN)->orderBy('name')->get();

        return view('backend.reports.rider_overview', compact('summary', 'riders', 'statusLabels', 'request', 'allRiders'));
    }

    public function completedDeliveries(Request $request)
    {
        $baseQuery = Parcel::query()
            ->leftJoin('users', 'users.id', '=', 'parcels.delivery_man_id')
            ->where('parcels.status', ParcelStatus::MARKETPLACE_DELIVERED)
            ->select('parcels.*', 'users.name as rider_name');

        if ($request->filled('rider_id')) {
            $baseQuery->where('parcels.delivery_man_id', (int) $request->rider_id);
        }

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $baseQuery->whereBetween('parcels.updated_at', [$request->from_date, $request->to_date]);
        }

        $totalCompleted = (clone $baseQuery)->count();
        $parcels = $baseQuery->orderByDesc('parcels.updated_at')->paginate(20);
        $riders = User::where('user_type', UserType::DELIVERYMAN)->orderBy('name')->get();

        return view('backend.reports.completed_deliveries', compact('parcels', 'totalCompleted', 'riders', 'request'));
    }

    //salary reports
    public function salaryReports(Request $request)
    {
        return view('backend.reports.salary_reports', compact('request'));
    }

    public function ReportssalaryReports(Request $request)
    {
        $totalSalary       = $this->repo->salaryReports($request);
        $salaries          = $totalSalary['salary'];
        $salaryPayments    = $totalSalary['salaryPayment'];


        return view('backend.reports.salary_reports', compact('request', 'salaries', 'salaryPayments'));
    }

    public function SalaryReportPrint(Request $request)
    {

        $totalSalary       = $this->repo->salaryReports($request);
        $salaries          = $totalSalary['salary'];
        $salaryPayments    = $totalSalary['salaryPayment'];

        return view('backend.reports.salary_reports_print', compact('request', 'salaries', 'salaryPayments'));
    }
    //export
    public function MerchantReportExport(Request $request)
    {
        return  Excel::download(new MerchantReports, 'MerchantReports.xlsx');
    }
}
