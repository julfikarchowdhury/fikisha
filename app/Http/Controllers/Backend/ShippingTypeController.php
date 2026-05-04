<?php

namespace App\Http\Controllers\Backend;

use App\Enums\DeliveryType;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\ShippingType\StoreRequest;
use App\Http\Requests\ShippingType\UpdateRequest;
use App\Repositories\DeliveryType\DeliveryTypeInterface;
use App\Repositories\ShippingType\ShippingTypeInterface;
use Brian2694\Toastr\Facades\Toastr;

class ShippingTypeController extends Controller
{
    protected $repo, $deliveryTypeRepo;
    public function __construct(ShippingTypeInterface $repo, DeliveryTypeInterface $deliveryTypeRepo)
    {
        $this->repo               = $repo;
        $this->deliveryTypeRepo   = $deliveryTypeRepo;
    }

    public function index()
    {
        $inside_shipping_types = $this->repo->insideShippingTypes();
        $outside_shipping_types = $this->repo->outsideShippingTypes();
        return view('backend.shippingtype.index', compact('inside_shipping_types', 'outside_shipping_types'));
    }

    public function create()
    {
        $delivery_types = $this->deliveryTypeRepo->getActive();
        return view('backend.shippingtype.create', compact('delivery_types'));
    }

    public function getShippingType(Request $request)
    {
        if (request()->ajax()) :
            if ($request->from_state_id == $request->to_state_id) :
                $request['delivery_type_id'] = DeliveryType::SAMEDAY;
            else :
                $request['delivery_type_id'] = DeliveryType::SUBCITY;
            endif;
            $shipping_types = $this->repo->getActive($request);


            $options  = '';
            foreach ($shipping_types as $key => $shipping_type) {
                $options .= '<option value="' . $shipping_type->id . '" data-weight="' . $shipping_type->BasicWeightFrameValue . '" data-volume="' . $shipping_type->BasicVolumeFrameValue . '" data-wamount="' . $shipping_type->addi_weight_price . '" data-vamount="' . $shipping_type->addi_volume_price . '">' . $shipping_type->title . '</option>';
            }
            return $options;
        endif;
        return '';
    }

    public function getShippingTypes(Request $request)
    {
        if (request()->ajax()) :
            $shipping_types = $this->repo->getActive($request);
            $options  = '';
            $options .= '<option value=""> Select shipping type</option>';
            foreach ($shipping_types as $key => $shipping_type) {
                $options .= '<option value="' . $shipping_type->id . '">' . $shipping_type->title . '</option>';
            }
            return $options;
        endif;
        return '';
    }

    public function store(StoreRequest $request)
    {

        if ($this->repo->store($request)) {
            Toastr::success('Shipping Type successfully added.', __('message.success'));
            return redirect()->route('shipping-type.index');
        } else {
            Toastr::error('Something went wrong.', __('message.error'));
            return redirect()->back();
        }
    }

    public function edit($id)
    {
        $shipping_type = $this->repo->get($id);
        $delivery_types = $this->deliveryTypeRepo->getActive();
        return view('backend.shippingtype.edit', compact('shipping_type', 'delivery_types'));
    }

    public function update(UpdateRequest $request)
    {

        if ($this->repo->update($request)) {
            Toastr::success('Shipping type successfully updated.', __('message.success'));
            return redirect()->route('shipping-type.index');
        } else {
            Toastr::error('Something went wrong.', __('message.error'));
            return redirect()->back();
        }
    }

    public function destroy($id)
    {
        $this->repo->delete($id);
        Toastr::success('Shipping type successfully deleted.', __('message.success'));
        return back();
    }
}
