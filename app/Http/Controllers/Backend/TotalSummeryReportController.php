<?php

namespace App\Http\Controllers\Backend;
 
use App\Enums\ParcelStatus;
use App\Http\Controllers\Controller;
use App\Models\Backend\Parcel;
use App\Models\Backend\PlatformLedgerTransaction;
use App\Repositories\DeliveryMan\DeliveryManInterface;
use App\Repositories\BankTransaction\BankTransactionInterface;
use App\Repositories\Merchant\MerchantInterface;
use App\Repositories\Reports\TotalSummeryReport\TotalSummeryReportInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TotalSummeryReportController extends Controller
{
    protected $repo;
    protected $merchant;
    protected $deliveryman;
    protected $bankTransaction;
    public function __construct(TotalSummeryReportInterface $repo, MerchantInterface $merchant,BankTransactionInterface $bankTransaction,DeliveryManInterface $deliveryman)
    {
        $this->repo         =  $repo;
        $this->merchant     =  $merchant;
        $this->deliveryman  =  $deliveryman;
        $this->bankTransaction     =  $bankTransaction;
    }

    public function parcelTotalSummery(Request $request){
        $from = Carbon::now()->startOfDay()->toDateTimeString();
        $to = Carbon::now()->endOfDay()->toDateTimeString();

        $summary = $this->buildMarketplaceSummary($request, [$from, $to]);
        $riderSummaries = $this->buildRiderSummary($request, [$from, $to]);

        $request->merge([
            'parcel_date' => Carbon::now()->format('m/d/Y') . ' To ' . Carbon::now()->format('m/d/Y'),
        ]);

        return view('backend.reports.parcel-total.parcel_total_reports', compact('request', 'summary', 'riderSummaries'));
    }

    public function parcelTotalSummeryFilter(Request $request){
        $dateRange = null;
        if ($request->parcel_date) {
            $date = explode('To', $request->parcel_date);
            if (is_array($date) && count($date) === 2) {
                $from = Carbon::parse(trim($date[0]))->startOfDay()->toDateTimeString();
                $to = Carbon::parse(trim($date[1]))->endOfDay()->toDateTimeString();
                $dateRange = [$from, $to];
            }
        }

        $summary = $this->buildMarketplaceSummary($request, $dateRange);
        $riderSummaries = $this->buildRiderSummary($request, $dateRange);

        return view('backend.reports.parcel-total.parcel_total_reports', compact('request', 'summary', 'riderSummaries'));

    }

    private function buildMarketplaceSummary(Request $request, ?array $dateRange): array
    {
        $parcelsQuery = Parcel::query()
            ->where('status', ParcelStatus::MARKETPLACE_DELIVERED);

        if ($request->parcel_merchant_id) {
            $parcelsQuery->where('merchant_id', $request->parcel_merchant_id);
        }

        if ($dateRange) {
            $parcelsQuery->whereBetween('updated_at', $dateRange);
        }

        $parcels = $parcelsQuery->get();

        $ledgerQuery = PlatformLedgerTransaction::query()->where('direction', 'debit');
        if ($dateRange) {
            $ledgerQuery->whereBetween('created_at', $dateRange);
        }
        $totalPlatformRefunds = (float) $ledgerQuery->sum('amount');

        return [
            'total_delivered' => $parcels->count(),
            'total_base_charge' => $parcels->sum('base_delivery_charge'),
            'total_receiver_markup' => $parcels->sum('receiver_markup'),
            'total_final_paid' => $parcels->sum('final_paid_amount'),
            'total_commission' => $parcels->sum('commission_amount'),
            'total_rider_earning' => $parcels->sum('rider_earning'),
            'total_platform_earning' => $parcels->sum('platform_total_earning'),
            'total_platform_refunds' => $totalPlatformRefunds,
            'total_platform_net' => round(((float) $parcels->sum('platform_total_earning')) - $totalPlatformRefunds, 2),
        ];
    }

    private function buildRiderSummary(Request $request, ?array $dateRange)
    {
        $riderSummaryQuery = Parcel::query()
            ->leftJoin('users', 'users.id', '=', 'parcels.delivery_man_id')
            ->where('parcels.status', ParcelStatus::MARKETPLACE_DELIVERED)
            ->selectRaw('parcels.delivery_man_id as rider_id, users.name as rider_name, COUNT(*) as total_deliveries, SUM(parcels.rider_earning) as total_earned, SUM(parcels.commission_amount) as total_commission, SUM(parcels.platform_total_earning) as total_platform_earning')
            ->groupBy('parcels.delivery_man_id', 'users.name');

        if ($request->parcel_merchant_id) {
            $riderSummaryQuery->where('parcels.merchant_id', $request->parcel_merchant_id);
        }

        if ($dateRange) {
            $riderSummaryQuery->whereBetween('parcels.updated_at', $dateRange);
        }

        return $riderSummaryQuery->orderByDesc('total_deliveries')->get();
    }


}
