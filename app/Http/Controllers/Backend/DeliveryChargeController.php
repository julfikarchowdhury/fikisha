<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\DeliveryCharge\UpdateRequest;
use App\Models\Backend\GeneralSettings;
use App\Repositories\DeliveryCharge\DeliveryChargeInterface;
use App\Repositories\Merchant\MerchantInterface;
use App\Repositories\Parcel\ParcelInterface;
use Brian2694\Toastr\Facades\Toastr;
class DeliveryChargeController extends Controller
{
    protected $repo,$parcelRepo,$merchantRepo;
    public function __construct(
        DeliveryChargeInterface $repo,
        ParcelInterface $parcelRepo,
        MerchantInterface $merchantRepo
        )
    {
        $this->repo         = $repo;
        $this->parcelRepo   = $parcelRepo;
        $this->merchantRepo = $merchantRepo;
    }

    public function index(Request $request)
    {
        return redirect()->route('delivery-charge.create');
    }
    public function filter(Request $request)
        {
            $delivery_charges = $this->repo->filter($request);
            $categories = $this->repo->categories();
            
            return view('backend.delivery_charge.index',compact('delivery_charges','categories','request'));
        }

    public function create()
    {
        $settings = GeneralSettings::find(1);
        return view('backend.delivery_charge.create', compact('settings'));
    }

    public function store(Request $request)
    {
        $mode = (string) $request->input('marketplace_pricing_mode', 'city');
        if ($mode !== 'city') {
            $mode = 'city';
        }
        $rules = [
            'marketplace_pricing_mode' => 'required|in:distance,city',
            'marketplace_receiver_markup_percent' => 'required|numeric|min:0|max:100',
        ];

        $rules += [
            'inside_city_distance' => 'required|numeric|min:0',
            'inside_city_base_fare' => 'required|numeric|min:0',
            'inside_city_per_km_rate' => 'required|numeric|min:0',
            'inside_city_per_kg_rate' => 'required|numeric|min:0',
            'outside_city_base_fare' => 'required|numeric|min:0',
            'outside_city_per_km_rate' => 'required|numeric|min:0',
            'outside_city_per_kg_rate' => 'required|numeric|min:0',
        ];

        $request->validate($rules);

        $settings = GeneralSettings::find(1);
        if (!$settings) {
            $settings = new GeneralSettings();
            $settings->id = 1;
        }

        $settings->marketplace_pricing_mode = $mode;
        $settings->marketplace_base_fare = (float) ($request->marketplace_base_fare ?? 0);
        $settings->marketplace_per_km_rate = (float) ($request->marketplace_per_km_rate ?? 0);
        $settings->marketplace_per_kg_rate = (float) ($request->marketplace_per_kg_rate ?? 0);
        $settings->marketplace_receiver_markup_percent = (float) $request->marketplace_receiver_markup_percent;
        $settings->inside_city_distance = (float) ($request->inside_city_distance ?? 0);
        $settings->inside_city_base_fare = (float) ($request->inside_city_base_fare ?? 0);
        $settings->inside_city_per_km_rate = (float) ($request->inside_city_per_km_rate ?? 0);
        $settings->inside_city_per_kg_rate = (float) ($request->inside_city_per_kg_rate ?? 0);
        $settings->outside_city_base_fare = (float) ($request->outside_city_base_fare ?? 0);
        $settings->outside_city_per_km_rate = (float) ($request->outside_city_per_km_rate ?? 0);
        $settings->outside_city_per_kg_rate = (float) ($request->outside_city_per_kg_rate ?? 0);
        $settings->save();

        Toastr::success('Marketplace pricing saved.', __('message.success'));
        return redirect()->route('delivery-charge.create');
    }

    public function edit($id)
    {
        $categories         = $this->repo->categories();
        $delivery_charge    = $this->repo->get($id);
        $deliveryTypes      = $this->parcelRepo->deliveryTypes();
        $countries          = $this->merchantRepo->all_country(); 
        return view('backend.delivery_charge.edit',compact('delivery_charge', 'categories','deliveryTypes','countries'));
    }

    public function update(UpdateRequest $request)
    {

        if($this->repo->update($request)){
            Toastr::success(__('delivery_charge.update_msg'),__('message.success'));
            return redirect()->route('delivery-charge.index');
        }else{
            Toastr::error(__('delivery_charge.error_msg'),__('message.error'));
            return redirect()->back();
        }

    }

    public function destroy($id)
    {
        $this->repo->delete($id);
        Toastr::success(__('delivery_charge.delete_msg'),__('message.success'));
        return back();
    }
 
    
}
