<?php

namespace App\Http\Controllers\Backend;

use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Models\Config;
use App\Repositories\DeliveryType\DeliveryTypeInterface;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;

class DeliveryTypeController extends Controller
{

    protected $repo;
    public function __construct(DeliveryTypeInterface $repo)
    {
        $this->repo = $repo;
    }
    public function index(){
        $delivery_types   = $this->repo->get();
        return view('backend.delivery_type.index',compact('delivery_types'));
    }
  
    public function create()
    {
        return view('backend.delivery_type.create');
    }

    public function store(Request $request)
    {
        if($this->repo->store($request)){
            Toastr::success('Delivery type successfully added.',__('message.success'));
            return redirect()->route('delivery-type.index');
        }else{
            Toastr::error('Something went wrong.',__('message.error'));
            return redirect()->back();
        }
    }

    public function edit($id)
    {
        $delivery_type = $this->repo->getFind($id);
        return view('backend.delivery_type.edit',compact('delivery_type'));
    }

    public function update(Request $request)
    {

        if($this->repo->update($request)){
            Toastr::success('Delivery type successfully updated.',__('message.success'));
            return redirect()->route('delivery-type.index');
        }else{
            Toastr::error('Something went wrong.',__('message.error'));
            return redirect()->back();
        }

    }

    public function destroy($id)
    {
        $this->repo->delete($id);
        Toastr::success('Shipping type successfully deleted.',__('message.success'));
        return back();
    }

}
