<?php

namespace App\Http\Controllers\Backend;

use App\Enums\DeliveryType;
use App\Enums\ParcelStatus;
use App\Enums\Status;
use App\Enums\UserType;
use App\Enums\WhoPays;
use App\Exports\ParcelSampleExport;
use App\Helpers\DeliveryChargeHelper;
use App\Http\Controllers\Controller;
use App\Imports\ParcelImport;
use App\Models\Backend\DeliveryCharge;
use App\Models\Backend\Merchant;
use App\Models\Backend\MpesaPayment;
use App\Models\MerchantShops;
use App\Repositories\Merchant\MerchantInterface;
use App\Repositories\MerchantPanel\Shops\ShopsInterface;
use Illuminate\Http\Request;
use App\Http\Requests\Parcel\StoreRequest;
use App\Http\Requests\Parcel\UpdateRequest;
use App\Models\Backend\City;
use App\Models\Backend\Country;
use App\Models\Backend\District;
use App\Models\Backend\Parcel;
use App\Models\Backend\ParcelEvent;
use App\Models\Backend\Receiver;
use App\Models\Backend\SenderCustomer;
use App\Models\User;
use App\Repositories\DeliveryMan\DeliveryManInterface;
use App\Repositories\DeliveryType\DeliveryTypeInterface;
use App\Repositories\Parcel\ParcelInterface;
use App\Repositories\ParcelCategory\ParcelCategoryInterface;
use App\Repositories\Province\ProvinceInterface;
use App\Repositories\ShippingType\ShippingTypeInterface;
use Illuminate\Support\Facades\Validator;
use Brian2694\Toastr\Facades\Toastr;
use Maatwebsite\Excel\Facades\Excel;

class ParcelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    protected $merchant;
    protected $repo, $parcelCategoryRepo;
    protected $shop, $deliveryTypeRepo, $shippingTypeRepo, $deliveryman, $provinceRepo;
    public function __construct(
        ParcelInterface $repo,
        MerchantInterface $merchant,
        ShopsInterface $shop,
        DeliveryManInterface $deliveryman,
        DeliveryTypeInterface $deliveryTypeRepo,
        ShippingTypeInterface $shippingTypeRepo,
        ParcelCategoryInterface $parcelCategoryRepo,
        ProvinceInterface $provinceRepo,
    ) {
        $this->merchant          = $merchant;
        $this->repo              = $repo;
        $this->shop              = $shop;
        $this->deliveryman       = $deliveryman;
        $this->deliveryTypeRepo  = $deliveryTypeRepo;
        $this->shippingTypeRepo  = $shippingTypeRepo;
        $this->parcelCategoryRepo = $parcelCategoryRepo;
        $this->provinceRepo = $provinceRepo;
    }

    public function index(Request $request)
    {
        $parcels        = $this->repo->all();
        $deliverymans   = $this->deliveryman->all();
        $shippingTypes  = $this->shippingTypeRepo->getAll();

        return view('backend.parcel.index', compact('parcels', 'deliverymans', 'request', 'shippingTypes'));
    }

    public function filter(Request $request)
    {
        if ($this->repo->filter($request)) {
            $parcels      = $this->repo->filter($request);
            $parcelsPrint = $this->repo->filterPrint($request);
            $deliverymans = $this->deliveryman->all();
            $shippingTypes  = $this->shippingTypeRepo->getAll();
            $request['filter'] = 'on';
            return view('backend.parcel.index', compact('parcels', 'deliverymans', 'request', 'parcelsPrint', 'shippingTypes'));
        } else {
            return redirect()->back();
        }
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create(Request $request)
    {
        $merchants              = $this->merchant->gatAll();
        $customers              = User::where('user_type', UserType::MERCHANT)->get();
        $packagings             = $this->repo->packaging();
        $cities                 = $this->merchant->all_city();
        $parcelCategories       = $this->parcelCategoryRepo->all(Status::ACTIVE, []);
        $data['provinces']      = $this->provinceRepo->getActive();
        return view('backend.parcel.create', $data, compact('cities', 'parcelCategories', 'merchants', 'packagings', 'customers'));
    }

    public function addItem(Request $request)
    {
        $item_number = $request->item_number;
        $parcelCategories = $this->parcelCategoryRepo->all(Status::ACTIVE, []);
        $packagings             = $this->repo->packaging();
        if ($request->type && $request->type == 'clone') :
            $view = view('backend.parcel.parcel_item_clone', compact('item_number', 'parcelCategories', 'request', 'packagings'))->render();
        else :
            $view = view('backend.parcel.parcel_item', compact('item_number', 'parcelCategories', 'packagings'))->render();
        endif;
        return response()->json([
            'view' => $view,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        if ($request->from_state_id == $request->to_state_id) :
            $request['delivery_type_id'] = DeliveryType::SAMEDAY;
        else :
            $request['delivery_type_id'] = DeliveryType::SUBCITY;
        endif;

        $whoPays = (int) ($request->who_pays_either ?? 0);
        $paymentIntent = (string) ($request->payment_intent ?? 'pay_now');
        if ($whoPays === WhoPays::SENDER && $paymentIntent === 'pay_now') {
            $checkoutId = trim((string) ($request->mpesa_checkout_request_id ?? ''));
            if ($checkoutId === '') {
                Toastr::error('M-Pesa prompt is required before saving this parcel.', __('message.error'));
                return redirect()->back()->withInput($request->all());
            }

            $payment = MpesaPayment::where('checkout_request_id', $checkoutId)->first();
            if (!$payment) {
                Toastr::error('M-Pesa payment request not found. Please retry M-Pesa payment.', __('message.error'));
                return redirect()->back()->withInput($request->all());
            }
        }

        if ($request->merchant_id) :
            $merchant      = Merchant::find($request->merchant_id);
        else :
            $merchant      = null;
        endif;

        if ($this->repo->store($request)) {
            Toastr::success(__('parcel.added_msg'), __('message.success'));
            return redirect()->route('parcel.index');
        } else {
            Toastr::error(__('parcel.error_msg'), __('message.error'));
            return redirect()->back()->withInput($request->all());
        }
    }


    public function show($id)
    {
        //
    }


    // Parcel logs
    public function logs($id)
    {
        $parcel         = $this->repo->get($id);
        $parcelevents   = $this->repo->parcelEvents($id);
        return view('backend.parcel.logs', compact('parcel', 'parcelevents'));
    }

    // Parcel duplicate
    public function duplicate($id)
    {
        $parcel                  = $this->repo->get($id);
        $customers              = User::where('user_type', UserType::MERCHANT)->get();
        $merchants              = $this->merchant->gatAll();
        $merchant_info                = $this->merchant->get($parcel->merchant_id);
        $shops                   = $this->shop->all($parcel->merchant_id);
        $packagings              = $this->repo->packaging();
        $cities                 = $this->merchant->all_city();
        $request                     = new Request();
        $request['delivery_type_id'] = $parcel->delivery_type_id;
        $parcelCategories = $this->parcelCategoryRepo->all(Status::ACTIVE, []);
        $shippingTypes                = $this->shippingTypeRepo->getActive($request);
        $data['provinces'] = $this->provinceRepo->getActive();
        return view('backend.parcel.duplicate', compact(
            'cities',
            'parcelCategories',
            'parcel',
            'merchant_info',
            'merchants',
            'shops',
            'packagings',
            'shippingTypes',
            'customers'
        ), $data);
    }

    public function duplicateStore(StoreRequest $request)
    {
        if ($request->from_state_id == $request->to_state_id) :
            $request['delivery_type_id'] = DeliveryType::SAMEDAY;
        else :
            $request['delivery_type_id'] = DeliveryType::SUBCITY;
        endif;

        if ($this->repo->duplicateStore($request)) {
            Toastr::success(__('parcel.added_msg'), __('message.success'));
            return redirect()->route('parcel.index');
        } else {
            Toastr::error(__('parcel.error_msg'), __('message.error'));
            return redirect()->back()->withInput($request->all());
        }
    }

    // Parcel details
    public function details($id)
    {
        $parcel         = $this->repo->details($id);
        $parcelevents   = ParcelEvent::where('parcel_id', $id)->orderBy('created_at', 'desc')->get();
        return view('backend.parcel.details', compact('parcel', 'parcelevents'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function edit($id)
    {
        $parcel          = $this->repo->get($id);
        $merchants              = $this->merchant->gatAll();
        $merchant_info        = $this->merchant->get($parcel->merchant_id);
        $shops           = $this->shop->all($parcel->merchant_id);
        $packagings              = $this->repo->packaging();
        $cities                 = $this->merchant->all_city();
        $parcelCategories = $this->parcelCategoryRepo->all(Status::ACTIVE, []);
        $request                     = new Request();
        $request['delivery_type_id'] = $parcel->delivery_type_id;
        $shippingTypes                = $this->shippingTypeRepo->getActive($request);
        $data['provinces'] = $this->provinceRepo->getActive();
        $customers              = User::where('user_type', UserType::MERCHANT)->get();
        return view('backend.parcel.edit', compact(
            'cities',
            'parcelCategories',
            'parcel',
            'merchants',
            'merchant_info',
            'shops',
            'packagings',
            'shippingTypes',
            'customers',
        ), $data);
    }

    // Parcel update
    public function statusUpdate($id, $status_id)
    {
        $this->repo->statusUpdate($id, $status_id);
        Toastr::success(__('parcel.update_msg'), __('message.success'));
        return redirect()->back();
    }

    public function ParcelCancel(Request $request)
    {
        if ($this->repo->ParcelCancel($request)) :
            Toastr::success(__('parcel.parcel_canceled_successfully'), __('message.success'));
            return redirect()->back();
        else :
            Toastr::error(__('parcel.error_msg'), __('message.error'));
            return redirect()->back()->withInput($request->all());
        endif;
    }

    public function ParcelStatusUpdate(Request $request)
    {
        if ($request->status == ParcelStatus::DELIVERY_MAN_ASSIGN1) :
            $validator = Validator::make($request->all(), [
                'delivery_man_id' => ['required']
            ]);
            if ($validator->fails()) :
                Toastr::error('Delivery man field is required.', __('message.error'));
                return redirect()->back()->withInput($request->all());
            endif;
        endif;

        if ($request->status == ParcelStatus::RETURN_ASSIGN_TO_MERCHANT1) :
            $validator = Validator::make($request->all(), [
                'date' => ['required']
            ]);
            if ($validator->fails()) :
                Toastr::error('Date field is required.', __('message.error'));
                return redirect()->back()->withInput($request->all());
            endif;
        endif;

        if ($this->repo->ParcelStatusUpdate($request->parcel_id, $request->status, $request)) :
            Toastr::success(__('parcel.Status_update_successfully'), __('message.success'));
            return redirect()->back();
        else :
            Toastr::error(__('parcel.error_msg'), __('message.error'));
            return redirect()->back()->withInput($request->all());
        endif;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, $id)
    {
        if ($request->from_state_id == $request->to_state_id) :
            $request['delivery_type_id'] = DeliveryType::SAMEDAY;
        else :
            $request['delivery_type_id'] = DeliveryType::SUBCITY;
        endif;

        if ($this->repo->update($id, $request)) {
            Toastr::success(__('parcel.update_msg'), __('message.success'));
            return redirect()->route('parcel.index');
        } else {
            Toastr::error(__('parcel.error_msg'), __('message.error'));
            return redirect()->back();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->repo->delete($id);
        Toastr::success(__('parcel.delete_msg'), __('message.success'));
        return redirect()->back();
    }

    public function parcelImportExport()
    {
        $deliveryCategories = $this->repo->deliveryCategories();
        return view('backend.parcel.import', compact('deliveryCategories'));
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
            $failures     = $e->failures();
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
        return redirect()->route('parcel.index');
    }

    public function getImportMerchant(Request $request)
    {
        $search   = $request->search;
        $response = array();
        if ($request->searchQuery == 'true') {
            if ($search == '') {
                $merchants = Merchant::where('status', Status::ACTIVE)
                    ->orderby('business_name', 'asc')
                    ->select('id', 'business_name', 'vat')
                    ->where('business_name', 'like', '%' . $search . '%')
                    ->limit(10)
                    ->get();
            } else {
                $merchants = Merchant::where('status', Status::ACTIVE)
                    ->orderby('business_name', 'asc')
                    ->select('id', 'business_name', 'vat')
                    ->where('business_name', 'like', '%' . $search . '%')
                    ->limit(10)
                    ->get();
            }

            foreach ($merchants as $merchant) {
                $response[] = array(
                    "id" => $merchant->id,
                    "text" => $merchant->id . ' = ' . $merchant->business_name,
                );
            }
            return response()->json($response);
        }
    }

    public function getMerchant(Request $request)
    {
        $search   = $request->search;
        $response = array();
        if ($request->searchQuery == 'true') {
            $merchants = Merchant::where('status', Status::ACTIVE)
                ->orderby('business_name', 'asc')
                ->select('id', 'business_name', 'vat')
                ->where('business_name', 'like', '%' . $search . '%')
                ->limit(10)
                ->get();

            foreach ($merchants as $merchant) {
                $response[] = array(
                    "id" => $merchant->id,
                    "text" => $merchant->business_name,
                );
            }
            return response()->json($response);
        } else {
            $merchant = Merchant::find($search);
            $response[] = array(
                "discount"    => $merchant->discount ?? 0,
                "vat"         => $merchant->vat ?? 0,
                "cod_charges" => $merchant->cod_charges,
                "discount_eligible" => $merchant->discount_eligible,
            );
            return response()->json($response);
        }
    }

    public function getMerchantCod(Request $request)
    {
        if (request()->ajax()) :
            $merchant = [];
            $merchant = Merchant::find($request->merchant_id);
            $merchant = [
                'inside_city'  => $merchant->cod_charges['inside_city'],
                'sub_city' => $merchant->cod_charges['sub_city'],
                'outside_city' => $merchant->cod_charges['outside_city']
            ];
            return response()->json($merchant);
        endif;
        return '';
    }

    public function merchantShops(Request $request)
    {
        if (request()->ajax()) {
            if ($request->id && $request->shop == 'true') {
                $merchantShops          = [];
                $merchantShop           = MerchantShops::where(['merchant_id' => $request->id, 'default_shop' => Status::ACTIVE])->first();
                $merchantShops[]        = $merchantShop;
                $merchantShopArray      = MerchantShops::where(['merchant_id' => $request->id, 'default_shop' => Status::INACTIVE])->get();
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
                $merchantShop = MerchantShops::with('merchant')->find($request->id);
                if (!blank($merchantShop)) {
                    return $merchantShop;
                }
                return '';
            }
        }
        return '';
    }

    // All delivery charge functions
    public function deliveryChargeUnitParcel(Request $request)
    {
        return 0;
    }

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
                    return view('backend.parcel.deliveryWeight', compact('deliveryCharges'));
                }
                return '';
            }
        }
        return '';
    }

    //delivery man search
    public function deliverymanSearch(Request $request)
    {

        $search = $request->search;
        $province_id = $request->province_id;
        if ($request->single) {
            $deliveryMan  = ParcelEvent::where([
                'parcel_id' => $request->parcel_id,
                'parcel_status' => $request->status
            ])->first();

            if (isset($deliveryMan->deliveryMan) && !blank($deliveryMan->deliveryMan)) {
                $response = '<option value="' . $deliveryMan->delivery_man_id . '" selected> ' . $deliveryMan->deliveryMan->user->name . '</option>';
            } else {
                $response = '<option value="' . $deliveryMan->pickup_man_id . '" selected> ' . $deliveryMan->pickupman->user->name . '</option>';
            }
            return $response;
        } else {
          
                $delivery_mans = User::where('status', Status::ACTIVE)
                    ->orderby('name', 'asc')
                    ->select('id', 'name', 'mobile')
                    ->where('name', 'like', '%' . $search . '%')
                    ->where(function($query)use($province_id){
                        if($province_id):
                            $query->where('province_id', $province_id);
                        endif;
                    })
                    ->where('user_type', UserType::DELIVERYMAN)
                    ->get();
           
            $response = [];
            foreach ($delivery_mans as $deliveryman) {
                $response[] = array(
                    "id"  => $deliveryman->deliveryman->id,
                    "text" => $deliveryman->name . ' (' . $deliveryman->mobile . ')',
                );
            }
            return response()->json($response);
        }
    }

    public function PickupManAssigned(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'delivery_man_id' => 'required'
        ]);

        if ($validator->fails()) :
            Toastr::error(__('parcel.required'), __('message.error'));
            return redirect()->back()->withInput($request->all());
        endif;

        if ($this->repo->pickupdatemanAssigned($request->parcel_id, $request)) {
            Toastr::success(__('parcel.pickup_man_assigned'), __('message.success'));
            return redirect()->back();
        } else {
            Toastr::error(__('parcel.error_msg'), __('message.error'));
            return redirect()->back()->withInput($request->all());
        }
    }

    public function PickupManAssignedCancel(Request $request)
    {
        if ($this->repo->pickupdatemanAssignedCancel($request->parcel_id, $request)) {
            Toastr::success(__('parcel.pickup_man_assigned'), __('message.success'));
            return redirect()->back();
        } else {
            Toastr::error(__('parcel.error_msg'), __('message.error'));
            return redirect()->back()->withInput($request->all());
        }
    }

    public function readyToReassign(Request $request)
    {
        if ($this->repo->readyToReassign($request->parcel_id, $request)) {
            Toastr::success(__('parcel.ready_to_reassign_success'), __('message.success'));
            return redirect()->back();
        } else {
            Toastr::error(__('parcel.error_msg'), __('message.error'));
            return redirect()->back()->withInput($request->all());
        }
    }

    public function readyToReassignBooking(Request $request)
    {
        if ($this->repo->readyToReassignBooking($request->parcel_id, $request)) {
            Toastr::success(__('parcel.ready_to_reassign_booking_success'), __('message.success'));
            return redirect()->back();
        } else {
            Toastr::error(__('parcel.error_msg'), __('message.error'));
            return redirect()->back()->withInput($request->all());
        }
    }

    public function confirmedBooking(Request $request)
    {
        if ($this->repo->confirmedBooking($request->parcel_id, $request)) {
            Toastr::success(__('parcel.confirmed_booking_success'), __('message.success'));
            return redirect()->back();
        } else {
            Toastr::error(__('parcel.error_msg'), __('message.error'));
            return redirect()->back()->withInput($request->all());
        }
    }

    public function orderProcessing(Request $request)
    {
        if ($this->repo->orderProcessing($request->parcel_id, $request)) {
            Toastr::success(__('parcel.order_processing_success'), __('message.success'));
            return redirect()->back();
        } else {
            Toastr::error(__('parcel.error_msg'), __('message.error'));
            return redirect()->back()->withInput($request->all());
        }
    }

    public function PickupReSchedule(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'delivery_man_id' => 'required',
            'date' => 'required',
        ]);

        if ($validator->fails()) :
            Toastr::error(__('parcel.required'), __('message.error'));
            return redirect()->back()->withInput($request->all());
        endif;

        if ($this->repo->PickupReSchedule($request->parcel_id, $request)) {
            Toastr::success(__('parcel.pickup_scheduled'), __('message.success'));
            return redirect()->back();
        } else {
            Toastr::error(__('parcel.error_msg'), __('message.error'));
            return redirect()->back()->withInput($request->all());
        }
    }

    public function PickupReScheduleCancel(Request $request)
    {
        if ($this->repo->PickupReScheduleCancel($request->parcel_id, $request)) {
            Toastr::success(__('parcel.pickup_reschedule_canceled'), __('message.success'));
            return redirect()->back();
        } else {
            Toastr::error(__('parcel.error_msg'), __('message.error'));
            return redirect()->back()->withInput($request->all());
        }
    }

    public function receivedBypickupman(Request $request)
    {
        if ($this->repo->receivedBypickupman($request->parcel_id, $request)) {
            Toastr::success(__('parcel.received_by_pickup_success'), __('message.success'));
            return redirect()->back();
        } else {
            Toastr::error(__('parcel.error_msg'), __('message.error'));
            return redirect()->back()->withInput($request->all());
        }
    }

    public function receivedBypickupmanCancel(Request $request)
    {
        if ($this->repo->receivedBypickupmanCancel($request->parcel_id, $request)) {
            Toastr::success(__('parcel.received_by_pickup_cancel_success'), __('message.success'));
            return redirect()->back();
        } else {
            Toastr::error(__('parcel.error_msg'), __('message.error'));
            return redirect()->back()->withInput($request->all());
        }
    }

    public function search(Request $data)
    {
        return $this->repo->search($data);
    }

    public function searchDeliveryManAssingMultipleParcel(Request $data)
    {
        return $this->repo->searchDeliveryManAssingMultipleParcel($data);
    }

    public function searchExpense(Request $data)
    {
        return $this->repo->searchExpense($data);
    }

    public function searchIncome(Request $data)
    {
        return $this->repo->searchIncome($data);
    }


    public function deliveryManAssignMultipleParcel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'delivery_man_id' => 'required',
            'parcel_ids_'     => 'required',
        ]);

        if ($validator->fails()) :
            Toastr::error(__('parcel.required'), __('message.error'));
            return redirect()->back()->withInput($request->all());
        endif;

        if ($this->repo->deliveryManAssignMultipleParcel($request)) {
            Toastr::success(__('parcel.delivery_man_assign_success'), __('message.success'));
            $deliveryman = $this->deliveryman->get($request->delivery_man_id);
            $parcels    = $this->repo->bulkParcels($request->parcel_ids_);
            $bulk_type  = ParcelStatus::DELIVERY_MAN_ASSIGN;
            return view('backend.parcel.bulk_print', compact('parcels', 'deliveryman', 'bulk_type'));
        } else {
            Toastr::error(__('parcel.error_msg'), __('message.error'));
            return redirect()->back()->withInput($request->all());
        }
    }

    public function ParcelBulkAssignPrint(Request $request)
    {
        try {
            $deliveryman  = $this->deliveryman->get($request->delivery_man_id);
            $parcels      = $this->repo->bulkParcels($request->parcels);
            $bulk_type    = ParcelStatus::DELIVERY_MAN_ASSIGN;
            $reprint = true;
            return view('backend.parcel.bulk_print', compact('parcels', 'deliveryman', 'bulk_type', 'reprint'));
        } catch (\Throwable $th) {
            Toastr::error(__('parcel.error_msg'), __('message.error'));
            return redirect()->back()->withInput($request->all());
        }
    }

    public function deliverymanAssign(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'delivery_man_id' => 'required'
        ]);

        if ($validator->fails()) :
            Toastr::error(__('parcel.required'), __('message.error'));
            return redirect()->back()->withInput($request->all());
        endif;

        if ($this->repo->deliverymanAssign($request->parcel_id, $request)) {
            Toastr::success(__('parcel.delivery_man_assign_success'), __('message.success'));
            return redirect()->back();
        } else {
            Toastr::error(__('parcel.error_msg'), __('message.error'));
            return redirect()->back()->withInput($request->all());
        }
    }

    public function deliverymanAssignCancel(Request $request)
    {
        if ($this->repo->deliverymanAssignCancel($request->parcel_id, $request)) {
            Toastr::success(__('parcel.deliveryman_assign_cancel'), __('message.success'));
            return redirect()->back();
        } else {
            Toastr::error(__('parcel.error_msg'), __('message.error'));
            return redirect()->back()->withInput($request->all());
        }
    }

    public function deliveryReschedule(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'delivery_man_id' => 'required',
            'date'           => 'required'
        ]);

        if ($validator->fails()) :
            Toastr::error(__('parcel.required'), __('message.error'));
            return redirect()->back()->withInput($request->all());
        endif;

        if ($this->repo->deliveryReschedule($request->parcel_id, $request)) {
            Toastr::success(__('parcel.delivery_reschedule_success'), __('message.success'));
            return redirect()->back();
        } else {
            Toastr::error(__('parcel.error_msg'), __('message.error'));
            return redirect()->back()->withInput($request->all());
        }
    }

    public function deliveryReScheduleCancel(Request $request)
    {
        if ($this->repo->deliveryReScheduleCancel($request->parcel_id, $request)) {
            Toastr::success(__('parcel.delivery_re_schedule_cancel'), __('message.success'));
            return redirect()->back();
        } else {
            Toastr::error(__('parcel.error_msg'), __('message.error'));
            return redirect()->back()->withInput($request->all());
        }
    }

    public function receivedWarehouse(Request $request)
    {
        if ($this->repo->receivedWarehouse($request->parcel_id, $request)) {
            Toastr::success(__('parcel.received_warehouse_success'), __('message.success'));
            return redirect()->back();
        } else {
            Toastr::error(__('parcel.error_msg'), __('message.error'));
            return redirect()->back()->withInput($request->all());
        }
    }

    public function receivedWarehouseCancel(Request $request)
    {
        if ($this->repo->receivedWarehouseCancel($request->parcel_id, $request)) {
            Toastr::success(__('parcel.received_warehouse_cancel'), __('message.success'));
            return redirect()->back();
        } else {
            Toastr::error(__('parcel.error_msg'), __('message.error'));
            return redirect()->back()->withInput($request->all());
        }
    }

    public function returntoQourier(Request $request)
    {
        if ($this->repo->returntoQourier($request->parcel_id, $request)) {
            Toastr::success(__('parcel.return_to_qourier_success'), __('message.success'));
            return redirect()->back();
        } else {
            Toastr::error(__('parcel.error_msg'), __('message.error'));
            return redirect()->back()->withInput($request->all());
        }
    }

    public function returntoQourierCancel(Request $request)
    {
        if ($this->repo->returntoQourierCancel($request->parcel_id, $request)) {
            Toastr::success(__('parcel.received_warehouse_cancel'), __('message.success'));
            return redirect()->back();
        } else {
            Toastr::error(__('parcel.error_msg'), __('message.error'));
            return redirect()->back()->withInput($request->all());
        }
    }

    public function returnAssignToMerchant(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'delivery_man_id' => 'required',
            'date'           => 'required'
        ]);

        if ($validator->fails()) :
            Toastr::error(__('parcel.required'), 'error');
            return redirect()->back()->withInput($request->all());
        endif;

        if ($this->repo->returnAssignToMerchant($request->parcel_id, $request)) {
            Toastr::success(__('parcel.return_assign_to_merchant_success'), __('message.success'));
            return redirect()->back();
        } else {
            Toastr::error(__('parcel.error_msg'), __('message.error'));
            return redirect()->back()->withInput($request->all());
        }
    }

    public function returnAssignToMerchantCancel(Request $request)
    {
        if ($this->repo->returnAssignToMerchantCancel($request->parcel_id, $request)) {
            Toastr::success(__('parcel.return_assign_to_merchant_cancel_success'), __('message.success'));
            return redirect()->back();
        } else {
            Toastr::error(__('parcel.error_msg'), __('message.error'));
            return redirect()->back()->withInput($request->all());
        }
    }

    public function returnAssignToMerchantReschedule(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'delivery_man_id' => 'required',
            'date'           => 'required'
        ]);

        if ($validator->fails()) :
            Toastr::error(__('parcel.required'), __('message.error'));
            return redirect()->back()->withInput($request->all());
        endif;

        if ($this->repo->returnAssignToMerchantReschedule($request->parcel_id, $request)) {
            Toastr::success(__('parcel.return_assign_to_merchant_reschedule_success'), __('message.success'));
            return redirect()->back();
        } else {
            Toastr::error(__('parcel.error_msg'), __('message.error'));
            return redirect()->back()->withInput($request->all());
        }
    }

    public function returnAssignToMerchantRescheduleCancel(Request $request)
    {
        if ($this->repo->returnAssignToMerchantRescheduleCancel($request->parcel_id, $request)) {
            Toastr::success(__('parcel.return_assign_to_merchant_reschedule_cancel_success'), __('message.success'));
            return redirect()->back();
        } else {
            Toastr::error(__('parcel.error_msg'), __('message.error'));
            return redirect()->back()->withInput($request->all());
        }
    }

    public function returnReceivedByMerchant(Request $request)
    {
        if ($this->repo->returnReceivedByMerchant($request->parcel_id, $request)) {
            Toastr::success(__('parcel.return_received_by_merchant'), __('message.success'));
            return redirect()->back();
        } else {
            Toastr::error(__('parcel.error_msg'), __('message.error'));
            return redirect()->back()->withInput($request->all());
        }
    }

    public function returnReceivedByMerchantCancel(Request $request)
    {
        if ($this->repo->returnReceivedByMerchantCancel($request->parcel_id, $request)) {
            Toastr::success(__('parcel.return_received_by_merchant_cancel_success'), __('message.success'));
            return redirect()->back();
        } else {
            Toastr::error(__('parcel.error_msg'), __('message.error'));
            return redirect()->back()->withInput($request->all());
        }
    }

    public function parcelDelivered(Request $request)
    {
        if ($this->repo->parcelDelivered($request->parcel_id, $request)) {
            Toastr::success(__('parcel.delivered_success'), __('message.success'));
            return redirect()->back();
        } else {
            Toastr::error(__('parcel.error_msg'), __('message.error'));
            return redirect()->back()->withInput($request->all());
        }
    }

    public function parcelDeliveredCancel(Request $request)
    {
        if ($this->repo->parcelDeliveredCancel($request->parcel_id, $request)) {
            Toastr::success(__('parcel.delivered_cancel'), __('message.success'));
            return redirect()->back();
        } else {
            Toastr::error(__('parcel.error_msg'), __('message.error'));
            return redirect()->back()->withInput($request->all());
        }
    }

    public function parcelPartialDelivered(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cash_collection'       => 'required',
        ]);

        if ($validator->fails()) {
            Toastr::error(__('parcel.required'), __('message.error'));
            return redirect()->back()->withInput($request->all());
        }

        if ($this->repo->parcelPartialDelivered($request->parcel_id, $request)) {
            Toastr::success(__('parcel.partial_delivered_success'), __('message.success'));
            return redirect()->back();
        } else {
            Toastr::error(__('parcel.error_msg'), __('message.error'));
            return redirect()->back()->withInput($request->all());
        }
    }

    public function parcelPartialDeliveredCancel(Request $request)
    {
        if ($this->repo->parcelPartialDeliveredCancel($request->parcel_id, $request)) {
            Toastr::success(__('parcel.partial_delivered_cancel'), __('message.success'));
            return redirect()->back();
        } else {
            Toastr::error(__('parcel.error_msg'), __('message.error'));
            return redirect()->back()->withInput($request->all());
        }
    }

    public function parcelPrint($id)
    {
        $parcel = $this->repo->get($id);
        $merchant = $this->merchant->get($parcel->merchant_id);
        $shops = $this->shop->all($parcel->merchant_id);
        return view('backend.parcel.print', compact('parcel', 'merchant', 'shops'));
    }

    public function returnParcelPrint($id)
    {
        $parcel = $this->repo->get($id);
        $merchant = $this->merchant->get($parcel->merchant_id);
        $shops = $this->shop->all($parcel->merchant_id);
        return view('backend.parcel.return_print', compact('parcel', 'merchant', 'shops'));
    }

    public function parcelPrintLabel($id)
    {
        $parcel = $this->repo->get($id);
        $merchant = $this->merchant->get($parcel->merchant_id);
        $shops = $this->shop->all($parcel->merchant_id);
        return view('backend.parcel.print-label', compact('parcel', 'merchant', 'shops'));
    }

    //multiple parcel label print
    public function parcelMultiplePrintLabel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'parcels' => 'required'
        ]);

        if ($validator->fails()) :
            Toastr::error('Must be select parcel.', __('message.error'));
            return redirect()->back()->withInput($request->all());
        endif;
        $parcels = $this->repo->parcelMultiplePrintLabel($request);
        return view('backend.parcel.multiple-print-label', compact('parcels'));
    }
    //end multiple parcel label print

    //Assign pickup bulk
    public function AssignPickupParcelSearch(Request $request)
    {
        if ($request->ajax()) {
            $merchant_id      = $request->merchant_id;
            $tracking_id      = $request->tracking_id;
            if ($merchant_id !== null && $tracking_id !== null) {
                $parcel      = Parcel::with(['merchant', 'merchant.user'])->where([
                    'merchant_id'     => $merchant_id,
                    'tracking_id'     => $tracking_id,
                    'status'          => ParcelStatus::PENDING
                ])->first();
                if ($parcel) {
                    return response()->json($parcel);
                } else {
                    return 0;
                }
            } else {
                return 0;
            }
        }
        return 0;
    }

    //assign pickup bulk store
    public function AssignPickupBulk(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'merchant_id'       => 'required',
            'delivery_man_id'   => 'required'
        ]);

        if ($validator->fails()) {
            Toastr::error(__('parcel.feild_required'), __('message.error'));
            return redirect()->back()->withInput($request->all());
        }

        if ($this->repo->pickupdatemanAssignedBulk($request)) {
            Toastr::success(__('parcel.pickup_man_assigned'), __('message.success'));
            return redirect()->back();
        } else {
            Toastr::error(__('parcel.error_msg'), __('message.error'));
            return redirect()->back()->withInput($request->all());
        }
    }


    //assign return to merchant
    //return to courier parcel will be show
    public function AssignReturnToMerchantParcelSearch(Request $request)
    {
        if ($request->ajax()) {
            $merchant_id      = $request->merchant_id;
            $tracking_id      = $request->tracking_id;
            if ($merchant_id !== null && $tracking_id !== null) {
                $parcel      = Parcel::with(['merchant', 'merchant.user'])->where([
                    'merchant_id'     => $merchant_id,
                    'tracking_id'     => $tracking_id,
                    'status'          => ParcelStatus::RETURN_TO_COURIER
                ])->first();
                if ($parcel) {
                    return response()->json($parcel);
                } else {
                    return 0;
                }
            } else {
                return 0;
            }
        }
        return 0;
    }

    //assign return to merchant bulk store
    public function AssignReturnToMerchantBulk(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'merchant_id'       => 'required',
            'delivery_man_id'   => 'required',
            'date'              => 'required'
        ]);

        if ($validator->fails()) {
            Toastr::error(__('parcel.feild_required'), __('message.error'));
            return redirect()->back()->withInput($request->all());
        }

        if ($this->repo->AssignReturnToMerchantBulk($request)) {
            Toastr::success(__('parcel.return_assign_to_merchant_success'), __('message.success'));
            $deliveryman    = $this->deliveryman->get($request->delivery_man_id);
            $parcels        = $this->repo->bulkParcels($request->parcel_ids);
            $bulk_type      = ParcelStatus::RETURN_ASSIGN_TO_MERCHANT;
            return view('backend.parcel.bulk_print', compact('parcels', 'deliveryman', 'bulk_type'));
        } else {
            Toastr::error(__('parcel.error_msg'), __('message.error'));
            return redirect()->back()->withInput($request->all());
        }
    }

    public function ParcelSearch(Request $request)
    {
        if ($this->repo->ParcelSearch($request)) {
            $parcels          = $this->repo->ParcelSearch($request);
            $deliverymans = $this->deliveryman->all();
            $shippingTypes  = $this->shippingTypeRepo->getAll();
            return view('backend.parcel.index', compact('parcels', 'request', 'deliverymans', 'shippingTypes'));
        } else {
            return redirect()->back()->withInput($request->all());
        }
    }

    //parcel sample export
    public function parcelSampleExport()
    {
        return Excel::download(new ParcelSampleExport, 'invoice.xlsx');
    }

    //deliveryman panel
    public function deliverymanParcelAccept(Request $request, $id)
    {
        $request['delivery_man_id']  = auth()->user()->deliveryman->id;
        if (auth()->user()->user_type == UserType::DELIVERYMAN) :
            if ($this->repo->pickupdatemanAssigned($id, $request)) {
                Toastr::success(__('parcel.parcel_accepted_successfully'), __('message.success'));
                return redirect()->back();
            } else {
                Toastr::error(__('parcel.error_msg'), __('message.error'));
                return redirect()->back()->withInput($request->all());
            }
        else :
            Toastr::error(__('parcel.error_msg'), __('message.error'));
            return redirect()->back()->withInput($request->all());
        endif;
    }

    public function parcelStatusUpdatePage(Request $request)
    {
        return view('backend.deliveryman_panel.parcel_status_update_modal', compact('request'));
    }

    public function deliverymanParcelStatusUpdate(Request $request)
    {
        $parcel = Parcel::where('id', $request->parcel_id)->where('status', ParcelStatus::DELIVERY_MAN_ASSIGN)->first();
        if ($parcel) :
            switch ($request->status_action) {
                    //return to qourier
                case ParcelStatus::RETURN_TO_COURIER:
                    if ($this->repo->returntoQourier($request->parcel_id, $request)) :
                        Toastr::success(__('parcel.return_to_qourier_success'), __('message.success'));
                        return redirect()->back();
                    else :
                        Toastr::success(__('parcel.error_msg'), __('message.error'));
                        return redirect()->back()->withInput($request->all());
                    endif;
                    break;

                    //partial delivered
                case ParcelStatus::PARTIAL_DELIVERED:
                    if ($this->repo->parcelPartialDelivered($request->parcel_id, $request)) :
                        Toastr::success(__('parcel.partial_delivered_success'), __('message.success'));
                        return redirect()->back();
                    else :
                        Toastr::success(__('parcel.error_msg'), __('message.error'));
                        return redirect()->back()->withInput($request->all());
                    endif;
                    break;

                    //delivered
                case ParcelStatus::DELIVERED:
                    if ($this->repo->parcelDelivered($request->parcel_id, $request)) :
                        Toastr::success(__('parcel.delivered_success'), __('message.success'));
                        return redirect()->back();
                    else :
                        Toastr::success(__('parcel.error_msg'), __('message.error'));
                        return redirect()->back()->withInput($request->all());
                    endif;
                    break;
                default:
                    Toastr::success(__('parcel.error_msg'), __('message.error'));
                    return redirect()->back()->withInput($request->all());
                    break;
            }
        else :
            Toastr::error(__('parcel.error_msg'), __('message.error'));
            return redirect()->back()->withInput($request->all());
        endif;
    }

    public function toCountries(Request $request)
    {
        $chargeLocations  = $this->merchant->toCountries($request)->groupBy('to_country_id');
        $option = '<option value="" selected>' . __('levels.select') . ' ' . __('parcel.country') . '</option>';
        if ($chargeLocations->count() > 0) :
            foreach ($chargeLocations as $key => $locations) {
                $country  = Country::find($key);
                $option .= '<option value="' . $key . '">' . $country->name . '</option>';
            }
        endif;
        return $option;
    }

    public function toCities(Request $request)
    {
        $chargeLocations  = $this->merchant->toCities($request)->groupBy('to_city_id');
        $option = '<option value="" selected>' . __('levels.select') . ' ' . __('parcel.city') . '</option>';
        if ($chargeLocations->count() > 0) :
            foreach ($chargeLocations as $key => $locations) {
                $city  = City::find($key);
                $option .= '<option value="' . $key . '">' . $city->name . '</option>';
            }
        endif;
        return $option;
    }

    public function toDistrict(Request $request)
    {
        $chargeLocations  = $this->merchant->toDistrict($request)->groupBy('to_district_id');
        $option = '<option value="" selected>' . __('levels.select') . ' ' . __('parcel.district') . '</option>';
        if ($chargeLocations->count() > 0) :
            foreach ($chargeLocations as $key => $locations) {
                $district  = District::find($key);
                $option .= '<option value="' . $key . '">' . $district->name . '</option>';
            }
        endif;
        return $option;
    }

    public function toTown(Request $request)
    {
        $chargeLocations  = $this->merchant->toTown($request);
        $option = '<option value="" selected>' . __('levels.select') . ' ' . __('parcel.town') . '</option>';
        if ($chargeLocations->count() > 0) :
            foreach ($chargeLocations as $chargeLocation) {
                $option .= '<option value="' . $chargeLocation->id . '">' . $chargeLocation->toTown->name . '-' . $chargeLocation->to_portal_code . '</option>';
            }
        endif;
        return $option;
    }

    public function toPortalCode(Request $request)
    {
        $chargeLocations  = $this->merchant->toPortalCode($request);
        $value = "";
        if ($chargeLocations) :
            $value = @$chargeLocations->to_portal_code;
        endif;
        return $value;
    }

    public function parcelGetProvinceHub(Request $request)
    {
        if ($request->ajax()) {
            // City Data
            $cities =  City::where('province_id', $request->id)->get();
            $city_option = '<option value="">---' . __('levels.select') . ' ' . __('parcel.city') . '---</option>';
            if ($cities->count() > 0) :
                foreach ($cities as $city) {
                    $city_option .= '<option value="' . $city->id . '">' . $city->name . '</option>';
                }
            endif;
            $data['city_option'] = $city_option;
            return $data;
        }
        return [];
    }

    public function getProvinceAndAccountTypeWiseMerchant(Request $request)
    {
        if ($request->ajax()) {
            $merchants =  Merchant::where(function ($query) use ($request) {
                $query->whereHas('user', function ($queryEvent) use ($request) {
                    $queryEvent->where('province_id', $request->province_id);
                });
            })->where('account_type', $request->account_type)->get();

            $type_data = '';
            if ($request->type == 'from') {
                $type_data = __('parcel.sender');
            } elseif ($request->type == 'to') {
                $type_data = __('parcel.recipient');
            }

            if ($request->account_type == 1) {
                $merchant_option = '<option value="">---' . __('levels.select') . ' ' . $type_data . '---</option>';
                if ($merchants->count() > 0) :
                    foreach ($merchants as $merchant) {
                        if ($request->type == 'to') {
                            if ($merchant->id == $request->merchant_id) {
                                continue;
                            }
                        }
                        $merchant_option .= '<option value="' . $merchant->id . '">' . $merchant->user->name . ' (' . $merchant->user->mobile . ')</option>';
                    }
                endif;
                $data['merchant_option'] = $merchant_option;
            } elseif ($request->account_type == 2) {
                $merchant_option = '<option value="">---' . __('levels.select') . ' ' . $type_data . '---</option>';
                if ($merchants->count() > 0) :
                    foreach ($merchants as $merchant) {
                        if ($request->type == 'to') {
                            if ($merchant->id == $request->merchant_id) {
                                continue;
                            }
                        }
                        $merchant_option .= '<option value="' . $merchant->id . '">' . $merchant->business_name . ' (' . $merchant->user->mobile . ')</option>';
                    }
                endif;
                $data['merchant_option'] = $merchant_option;
            }
            $data['merchant_option'] = $merchant_option;
            return $data;
        }
        return [];
    }

    public function getMerchantCustomer(Request $request)
    {
        if ($request->ajax()) {
            $sender_customers_query = SenderCustomer::where([
                'sender_id' => $request->sender_id,
                'province_id' => $request->province_id,
            ]);
            if (!blank($request->account_type)) {
                $sender_customers_query->where('account_type', $request->account_type);
            }
            $sender_customers = $sender_customers_query->get();

            $customer_option = '<option value="">---' . __('levels.select') . ' ' . __('parcel.customer') . '---</option>';
            if ($sender_customers->count() > 0) :
                foreach ($sender_customers as $customer) {
                    $customer_option .= '<option value="' . $customer->id . '">' . $customer->name . ' (' . $customer->phone_number . ')</option>';
                }
            endif;
            return $customer_option;
        }
        return [];
    }

    public function parcelCustomerInfo(Request $request)
    {
        if ($request->ajax()) {
            return User::where('user_type', UserType::MERCHANT)->whereKey($request->id)->first();
        }
        return '';
    }

    // new update
    public function priorityUpdate(Request $request)
    {

        $parcel = Parcel::where(['id' => $request->id])->first();
        if (1 == (int)$request->priority) {
            $parcel->priority_type_id      =  2;
        } else {
            $parcel->priority_type_id      =  1;
        }
        $parcel->save();

        return $parcel;
    }

    // Parcel parcelDeliveryMan
    public function parcelDeliveryMan()
    {
        $parcelEvents = ParcelEvent::with('parcel')->whereNotNull('delivery_man_id')->where('parcel_status', ParcelStatus::DELIVERY_MAN_ASSIGN)->get();
        $mapParcels = [];
        if (!blank($parcelEvents)) {
            foreach ($parcelEvents as $key => $parcelEvent) {
                $mapParcels[$key]['deliveryMan'] = optional($parcelEvent->deliveryMan->user)->name;
                $mapParcels[$key]['deliveryPhone'] = optional($parcelEvent->deliveryMan->user)->mobile;
                $mapParcels[$key]['deliveryImage'] = optional($parcelEvent->deliveryMan->user)->image;
                $mapParcels[$key]['lat'] = $parcelEvent->delivery_lat;
                $mapParcels[$key]['long'] = $parcelEvent->delivery_long;
                $mapParcels[$key]['customer_name'] = $parcelEvent->parcel->customer_name;
                $mapParcels[$key]['customer_address'] = $parcelEvent->parcel->customer_address;
                $mapParcels[$key]['customer_phone'] = $parcelEvent->parcel->customer_phone;
                $mapParcels[$key]['merchant_business_name'] = $parcelEvent->parcel->merchant->business_name;
                $mapParcels[$key]['merchant_phone'] = $parcelEvent->parcel->merchant->user->mobile;
                $mapParcels[$key]['merchant_address'] = $parcelEvent->parcel->merchant->address;
                $mapParcels[$key]['current_payable'] = $parcelEvent->parcel->current_payable;
                $mapParcels[$key]['tracking_id'] = $parcelEvent->parcel->tracking_id;
                $mapParcels[$key]['url'] = route('parcel.logs', $parcelEvent->parcel->id);
            }
        }

        return view('backend.parcel.parcel-map-logs', compact('mapParcels'));
    }

    public function deliveredInfo($id)
    {
        $parcel         = $this->repo->get($id);
        $parcelevents   = $this->repo->parcelEvents($id);
        return view('backend.parcel.parcel-delivered-info', compact('parcel', 'parcelevents'));
    }

    public function receiverSuggestions(Request $request)
    {
        $receivers = Receiver::where(function ($query) use ($request) {
            $query->where('name', 'like', '%' . $request->search . '%');
            $query->orWhere('phone', 'like', '%' . $request->search . '%');
            $query->orWhere('address', 'like', '%' . $request->search . '%');
        })->select('name', 'phone', 'address')->get();
        return json_decode(response()->json($receivers)->content());
    }

    public function Placed($id)
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
        return view('backend.parcel.placed', compact('parcel', 'parcelevents', 'mapParcels'));
    }

    public function parcelReturnReport(Request $request)
    {
        $parcels        = $this->repo->parcelReturnReport($request);
        $shippingTypes  = $this->shippingTypeRepo->getAll();
        return view('backend.parcel.return_report', compact('parcels', 'shippingTypes', 'request'));
    }
}
