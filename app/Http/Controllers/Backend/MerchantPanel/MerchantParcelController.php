<?php

namespace App\Http\Controllers\Backend\MerchantPanel;

use App\Enums\DeliveryType;
use App\Enums\Status;
use App\Enums\WhoPays;
use App\Http\Controllers\Controller;
use App\Imports\ParcelImport;
use App\Models\Backend\DeliveryCharge;
use App\Models\Backend\MpesaPayment;
use App\Models\MerchantShops;
use App\Repositories\Merchant\MerchantInterface;
use App\Repositories\MerchantPanel\MerchantParcel\MerchantParcelInterface;
use App\Repositories\MerchantPanel\Shops\ShopsInterface;
use Illuminate\Http\Request;
use App\Http\Requests\MerchantPanel\Parcel\StoreRequest;
use App\Http\Requests\MerchantPanel\Parcel\UpdateRequest;
use Illuminate\Support\Facades\Auth;
use App\Enums\ParcelStatus;
use App\Exports\MerchantParcelExport;
use App\Models\Backend\SenderCustomer;
use App\Repositories\DeliveryType\DeliveryTypeInterface;
use App\Repositories\ParcelCategory\ParcelCategoryInterface;
use App\Repositories\Province\ProvinceInterface;
use App\Repositories\ShippingType\ShippingTypeInterface;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Helpers\DeliveryChargeHelper;
use App\Models\Backend\Parcel;
use App\Models\Backend\ParcelEvent;

class MerchantParcelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    protected $merchant;
    protected $repo, $parcelCategoryRepo;
    protected $shop, $deliveryTypeRepo, $shippingTypeRepo, $provinceRepo;
    public function __construct(
        MerchantParcelInterface $repo,
        MerchantInterface $merchant,
        ShopsInterface $shop,
        DeliveryTypeInterface $deliveryTypeRepo,
        ParcelCategoryInterface $parcelCategoryRepo,
        ShippingTypeInterface $shippingTypeRepo,
        ProvinceInterface $provinceRepo,
    ) {
        $this->merchant = $merchant;
        $this->repo = $repo;
        $this->shop = $shop;
        $this->deliveryTypeRepo   = $deliveryTypeRepo;
        $this->shippingTypeRepo   = $shippingTypeRepo;
        $this->parcelCategoryRepo = $parcelCategoryRepo;
        $this->provinceRepo = $provinceRepo;
    }

    public function index(Request $request)
    {
        $userID        = Auth::user()->id;
        $merchant      = $this->repo->getMerchant($userID);
        $parcels       = $this->repo->all($merchant->id);
        return view('backend.merchant_panel.parcel.index', compact('parcels', 'request'));
    }

    public function recievedParcels(Request $request)
    {
        $parcels = $this->repo->receivedParcels();
        return view('backend.merchant_panel.receiver.received_parcels', compact('parcels', 'request'));
    }

    public function parcelBank(Request $request)
    {
        $userID = Auth::user()->id;
        $merchant = $this->repo->getMerchant($userID);
        $parcels = $this->repo->parcelBank($merchant->id);
        return view('backend.merchant_panel.parcel.parcel_bank', compact('parcels', 'request'));
    }

    public function filter(Request $request)
    {
        $userID = Auth::user()->id;
        $merchant = $this->repo->getMerchant($userID);
        if ($this->repo->filter($merchant->id, $request)) {
            $parcels       = $this->repo->filter($merchant->id, $request);
            return view('backend.merchant_panel.parcel.index', compact('parcels', 'request'));
        } else {
            return redirect()->back();
        }
    }

    public function create(Request $request)
    {
        $userID = Auth::user()->id;
        $merchant = $this->repo->getMerchant($userID);
        $shops = $this->repo->getShops($merchant->id);
        $merchantShop = $shops[0];
        $packagings            = $this->repo->packaging();
        $cities       = $this->merchant->all_town();
        $hubs       = $this->merchant->all_city();
        $parcelCategories = $this->parcelCategoryRepo->all(Status::ACTIVE, []);
        $shipping_types     = $this->shippingTypeRepo->getAll();
        $data['provinces']      = $this->provinceRepo->getActive();
        $data['merchants']      = $this->merchant->gatAll();
        $data['customers']      = SenderCustomer::all();
        return view('backend.merchant_panel.parcel.create', compact('parcelCategories', 'merchant', 'merchantShop', 'shops', 'packagings', 'cities', 'hubs', 'shipping_types'), $data);
    }

    public function store(StoreRequest $request)
    {
        if ($request->from_state_id == $request->to_state_id) :
            $request['delivery_type_id'] = DeliveryType::SAMEDAY;
        else :
            $request['delivery_type_id'] = DeliveryType::SUBCITY;
        endif;

        $whoPays = (int) ($request->who_pays_either ?? 0);
        $whoPaysForPayment = $whoPays === WhoPays::RECIPIENT ? WhoPays::RECIPIENT : WhoPays::SENDER;
        $paymentIntent = (string) ($request->payment_intent ?? 'pay_now');
        if ($whoPaysForPayment === WhoPays::SENDER && $paymentIntent === 'pay_now') {
            $checkoutId = trim((string) ($request->mpesa_checkout_request_id ?? ''));
            if ($checkoutId === '') {
                Toastr::error('M-Pesa prompt is required before saving this parcel.', __('message.error'));
                return redirect()->back()->withInput($request->all());
            }

            $payment = MpesaPayment::where('checkout_request_id', $checkoutId)
                ->where('merchant_id', optional(Auth::user())->id)
                ->first();
            if (!$payment) {
                Toastr::error('M-Pesa payment request not found. Please retry M-Pesa payment.', __('message.error'));
                return redirect()->back()->withInput($request->all());
            }
        }

        $userID = Auth::user()->id;
        $merchant = $this->repo->getMerchant($userID);

        // dd($request->all());

        if ($this->repo->store($request, $merchant->id)) {
            Toastr::success(__('parcel.added_msg'), __('message.success'));
            return redirect()->route('merchant-panel.parcel.index');
        } else {
            Toastr::error(__('parcel.error_msg'), __('message.error'));
            return redirect()->back()->withInput($request->all());
        }
    }

    public function duplicateStore(StoreRequest $request)
    {
        if ($request->from_state_id == $request->to_state_id) :
            $request['delivery_type_id'] = DeliveryType::SAMEDAY;
        else :
            $request['delivery_type_id'] = DeliveryType::SUBCITY;
        endif;

        $userID = Auth::user()->id;
        $merchant = $this->repo->getMerchant($userID);
        if ($this->repo->duplicateStore($request, $merchant->id)) {
            Toastr::success(__('parcel.added_msg'), __('message.success'));
            return redirect()->route('merchant-panel.parcel.index');
        } else {
            Toastr::error(__('parcel.error_msg'), __('message.error'));
            return redirect()->back();
        }
    }


    public function show($id)
    {
        //
    }

    // Parcel logs
    public function logs($id)
    {
        $parcel       = $this->repo->get($id);
        $parcelevents = $this->repo->parcelEvents($id);
        return view('backend.merchant_panel.parcel.logs', compact('parcel', 'parcelevents'));
    }

    public function placed($id)
    {
        $parcel         = $this->repo->details($id);
        $parcelevents   = ParcelEvent::where('parcel_id', $id)->orderBy('created_at', 'desc')->get();
        $mapParcels = [];
        $mapParcels[0]['deliveryMan']   = 'a';
        $mapParcels[0]['deliveryPhone'] = '3';
        $mapParcels[0]['deliveryImage'] = '';
        $mapParcels[0]['lat']           = '27.2131811';
        $mapParcels[0]['long']          = '1.6475622';
        $mapParcels[0]['customer_name'] = 'a';
        $mapParcels[0]['customer_address']  = 'a';
        $mapParcels[0]['customer_phone']    = '3';
        $mapParcels[0]['merchant_business_name'] = $parcel->merchant->business_name;
        $mapParcels[0]['merchant_phone']         = $parcel->merchant->user->mobile;
        $mapParcels[0]['merchant_address']       = $parcel->merchant->address;
        $mapParcels[0]['current_payable']        = $parcel->current_payable;
        $mapParcels[0]['tracking_id']            = $parcel->tracking_id;
        $mapParcels[0]['url']                    = route('parcel.logs', $parcel->id);
        return view('backend.merchant_panel.parcel.placed', compact('parcel', 'parcelevents', 'mapParcels'));
    }

    // Parcel duplicate
    public function duplicate($id)
    {
        $parcel          = $this->repo->get($id);
        $merchant        = $this->merchant->get($parcel->merchant_id);
        $shops           = $this->shop->all($parcel->merchant_id);
        $packagings    = $this->repo->packaging();
        $cities       = $this->merchant->all_city();
        $deliveryTypes     = $this->deliveryTypeRepo->getActive();
        $request                     = new Request();
        $request['delivery_type_id'] = $parcel->delivery_type_id;
        $shippingTypes                = $this->shippingTypeRepo->getActive($request);
        $parcelCategories = $this->parcelCategoryRepo->all(Status::ACTIVE, []);
        $data['provinces']      = $this->provinceRepo->getActive();
        $data['merchants']      = $this->merchant->gatAll();
        $data['customers']      = SenderCustomer::all();
        return view('backend.merchant_panel.parcel.duplicate', compact(
            'cities',
            'parcelCategories',
            'parcel',
            'merchant',
            'deliveryTypes',
            'shops',
            'packagings',
            'shippingTypes'
        ), $data);
    }

    // Parcel details
    public function details($id)
    {
        $parcel       = $this->repo->details($id);
        $parcelevents = $this->repo->parcelEvents($id);
        return view('backend.merchant_panel.parcel.details', compact('parcel', 'parcelevents'));
    }

    // Parcel Print
    public function parcelPrint($id)
    {
        $parcel = $this->repo->get($id);
        $merchant = $this->merchant->get($parcel->merchant_id);
        return view('backend.merchant_panel.parcel.print', compact('parcel', 'merchant'));
    }

    public function edit($id)
    {
        $userID = Auth::user()->id;
        $parcel = $this->repo->get($id);
        if ($parcel->status == ParcelStatus::PENDING) {
            $merchant   = $this->repo->getMerchant($userID);
            $shops      = $this->repo->getShops($merchant->id);
            $packagings         = $this->repo->packaging();
            $cities       = $this->merchant->all_city();
            $deliveryTypes     = $this->deliveryTypeRepo->getActive();
            $request                     = new Request();
            $request['delivery_type_id'] = $parcel->delivery_type_id;
            $shippingTypes                = $this->shippingTypeRepo->getActive($request);
            $parcelCategories = $this->parcelCategoryRepo->all(Status::ACTIVE, []);
            $data['provinces']      = $this->provinceRepo->getActive();
            $data['merchants']      = $this->merchant->gatAll();
            $data['customers']      = SenderCustomer::all();
            return view('backend.merchant_panel.parcel.edit', compact(
                'cities',
                'parcelCategories',
                'parcel',
                'merchant',
                'deliveryTypes',
                'shippingTypes',
                'shops',
                'packagings'
            ), $data);
        } else {
            Toastr::error(__('parcel.edit_error_message'), __('message.error'));
            return redirect()->route('merchant-panel.parcel.index');
        }
    }

    // Parcel update
    public function statusUpdate($id, $status_id)
    {
        $this->repo->statusUpdate($id, $status_id);
        Toastr::success(__('parcel.update_msg'), __('message.success'));
        return redirect()->route('merchant-panel.parcel.index');
    }

    public function update(UpdateRequest $request, $id)
    {
        if ($request->from_state_id == $request->to_state_id) :
            $request['delivery_type_id'] = DeliveryType::SAMEDAY;
        else :
            $request['delivery_type_id'] = DeliveryType::SUBCITY;
        endif;

        $userID = Auth::user()->id;
        if ($this->repo->update($id, $request, $userID)) {
            Toastr::success(__('parcel.update_msg'), __('message.success'));
            return redirect()->route('merchant-panel.parcel.index');
        } else {
            Toastr::error(__('parcel.error_msg'), __('message.error'));
            return redirect()->back();
        }
    }


    public function destroy($id)
    {
        $userID = Auth::user()->id;
        $parcel = $this->repo->get($id);
        if ($parcel->status == ParcelStatus::PENDING) {
            $this->repo->delete($id, $userID);
            Toastr::success(__('parcel.delete_msg'), __('message.success'));
            return back();
        } else {
            Toastr::error(__('parcel.delete_error_message'), __('message.error'));
            return redirect()->route('merchant-panel.parcel.index');
        }
    }

    public function parcelImportExport()
    {
        $deliveryCategories = $this->repo->deliveryCategories();
        return view('backend.merchant_panel.parcel.import', compact('deliveryCategories'));
    }

    public function parcelImport(Request $request)
    {
        $request->validate([
            'file' => 'required',
        ]);
        try {
            $import = new ParcelImport();
            $import->import($request->file('file'));
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $importErrors = [];
            foreach ($failures as $failure) {
                $failure->row(); // row that went wrong
                $failure->attribute(); // either heading key (if using heading row concern) or column index
                $failure->errors(); // Actual error messages from Laravel validator
                $failure->values(); // The values of the row that has failed.
                $importErrors[$failure->row()][] = $failure->errors()[0];
            }
            return back()->with('importErrors', $importErrors);
        }
        Toastr::success(__('parcel.added_msg'), __('message.success'));
        return redirect()->route('merchant-panel.parcel.index');
    }

    public function merchantShops(Request $request)
    {
        if (request()->ajax()) {
            if ($request->id && $request->shop == 'true') {
                $merchantShops = [];
                $merchantShop = MerchantShops::where(['merchant_id' => $request->id, 'default_shop' => Status::ACTIVE])->first();
                $merchantShops[] = $merchantShop;
                $merchantShopArray = MerchantShops::where(['merchant_id' => $request->id, 'default_shop' => Status::INACTIVE])->get();
                if (!blank($merchantShopArray)) {
                    foreach ($merchantShopArray as $shop) {
                        $merchantShops[] = $shop;
                    }
                }
                if (!blank($merchantShops)) {
                    return view('backend.parcel.shops', compact('merchantShops'));
                }
                return '';
            } else {
                $merchantShop = MerchantShops::find($request->id);
                if (!blank($merchantShop)) {
                    return $merchantShop;
                }
                return '';
            }
        }
        return '';
    }



    // all delivery charge functions
    public function deliveryChargeUnitParcel(Request $request)
    {
        return 0;
    }

    // All charge function
    public function deliveryCharge(Request $request)
    {
        if (request()->ajax()) {
            return DeliveryChargeHelper::instance()->deliveryCharge($request);
        }
        return 0;
    }
    //  end delivery charge

    public function deliveryWeight(Request $request)
    {
        if (request()->ajax()) {
            if ($request->category_id) {
                $deliveryCharges = DeliveryCharge::where('category_id', $request->category_id)->get();
                if (!blank($deliveryCharges)) {
                    return view('backend.merchant_panel.parcel.deliveryWeight', compact('deliveryCharges'));
                }
                return '';
            }
        }
        return '';
    }

    public function parcelExport(Request $request)
    {
        try {
            if ($request->type && $request->type == 'csv') :
                return Excel::download(new MerchantParcelExport($this->repo->parcelExport($request)), 'Parcels Export-csv-file-' . Carbon::now()->format('d-m-Y His') . '.csv', \Maatwebsite\Excel\Excel::CSV, [
                    'Content-Type' => 'text/csv',
                ]);
            elseif ($request->type && $request->type == 'pdf') :
                $parcels  = $this->repo->parcelExport($request);
                $user = Auth::user();
                $pdf     = Pdf::loadView('backend.merchant_panel.parcel.parcel_export_pdf', ['parcels' => $parcels, 'user' => $user])->setPaper('A4', 'landscape');
                return  $pdf->download('Parcels Export-pdf-file-' . Carbon::now()->format('d-m-Y His') . '.pdf');
            else :
                return Excel::download(new MerchantParcelExport($this->repo->parcelExport($request)), 'Parcels Export-excel-file-' . Carbon::now()->format('d-m-Y His') . '.xlsx');
            endif;
        } catch (\Throwable $th) {
            Toastr::error(__('parcel.delete_error_message'), __('message.error'));
            return redirect()->back();
        }
    }


    public function activeLiveMonitoring(Request $request)
    {
        $userID   = Auth::user()->id;
        $merchant = $this->repo->getMerchant($userID);
        $parcels  = $this->repo->activeLiveMonitoring($merchant->id, $request);

        $deliveryTypes      = $this->deliveryTypeRepo->getActive();
        $shippingTypes      = $this->shippingTypeRepo->getActive($request);

        return view('backend.merchant_panel.parcel.active_live_monitoring', compact('userID', 'merchant', 'parcels', 'request', 'deliveryTypes', 'shippingTypes'));
    }

    public function  passiveMonitoring(Request $request)
    {

        $userID   = Auth::user()->id;
        $merchant = $this->repo->getMerchant($userID);
        $parcels  = $this->repo->passiveMonitoring($merchant->id, $request);

        $deliveryTypes      = $this->deliveryTypeRepo->getActive();
        $shippingTypes      = $this->shippingTypeRepo->getActive($request);

        return view('backend.merchant_panel.parcel.passive_monitoring', compact('userID', 'merchant', 'parcels', 'request', 'deliveryTypes', 'shippingTypes'));
    }


    public function parcelMonitoringExport(Request $request)
    {
        try {
            if ($request->monitoring == 'active_live_monitoring') :
                $parcels  = $this->repo->activeLiveMonitoring(Auth::user()->merchant->id, $request);
            elseif ($request->monitoring == 'passive_monitoring') :
                $parcels = $this->repo->passiveMonitoring(Auth::user()->merchant->id, $request);
            endif;
            if ($request->type && $request->type == 'csv') :
                return Excel::download(new MerchantParcelExport($parcels), 'Parcels Export-csv-file-' . Carbon::now()->format('d-m-Y His') . '.csv', \Maatwebsite\Excel\Excel::CSV, [
                    'Content-Type' => 'text/csv',
                ]);
            elseif ($request->type && $request->type == 'pdf') :
                $user = Auth::user();
                $pdf     = Pdf::loadView('backend.merchant_panel.parcel.parcel_export_pdf', ['parcels' => $parcels, 'user' => $user])->setPaper('A4', 'landscape');
                return  $pdf->download('Parcels Export-pdf-file-' . Carbon::now()->format('d-m-Y His') . '.pdf');

            else :
                return Excel::download(new MerchantParcelExport($parcels), 'Parcels Export-excel-file-' . Carbon::now()->format('d-m-Y His') . '.xlsx');
            endif;
        } catch (\Throwable $th) {
            Toastr::error(__('parcel.delete_error_message'), __('message.error'));
            return redirect()->back();
        }
    }
 
    public function getQoute(Request $request){
 
        $userID   = 4;
        $merchant = null;
        $shops    = null;

        $merchantShop = @$shops[0];
        $packagings             = $this->repo->packaging();
        $cities                  = $this->merchant->all_town();
        $hubs                    = $this->merchant->all_city();
        $parcelCategories        = $this->parcelCategoryRepo->all(Status::ACTIVE, []);
        $shipping_types          = $this->shippingTypeRepo->getAll();
        $data['provinces']      = $this->provinceRepo->getActive();
        $data['merchants']      = $this->merchant->gatAll();
        $data['customers']      = SenderCustomer::all();
        return view( 'backend.merchant_panel.parcel.get_qoute', compact('parcelCategories', 'merchant', 'merchantShop', 'shops', 'packagings', 'cities', 'hubs', 'shipping_types'), $data);
    
    }
   

}
