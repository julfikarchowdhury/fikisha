<?php

namespace App\Http\Controllers\Backend\MerchantPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Backend\Parcel;
use App\Repositories\Reports\ReportsInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class MerchantReportsController extends Controller
{
    protected $repo;
    public function __construct(ReportsInterface $repo)
    {
        $this->repo = $repo;
    }

    public function parcelReports(Request $request)
    {
        $parcels = [];
        return view('backend.merchant_panel.reports.parcel_reports', compact('request', 'parcels'));
    }

    public function parcelSReports(Request $request)
    {
        if ($this->repo->merchantParcelReports($request)) {
            $parcels      =  $this->repo->merchantParcelReports($request);
            $print        =   true;
            $parcel_ids   = '';
            foreach ($parcels as $key => $parcel) {
                $parcel_ids  = $parcel->id . ',' . $parcel_ids;
            }
            return view('backend.merchant_panel.reports.parcel_reports', compact('parcels', 'request', 'print', 'parcel_ids'));
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
        return view('backend.merchant_panel.reports.parcel_reports_print', compact('parcels'));
    }
}
