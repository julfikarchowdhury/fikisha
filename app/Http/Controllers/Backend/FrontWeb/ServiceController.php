<?php

namespace App\Http\Controllers\Backend\FrontWeb;

use App\Http\Controllers\Controller;
use App\Http\Requests\FrontWeb\Service\StoreRequest;
use App\Http\Requests\FrontWeb\Service\UpdateRequest;
use App\Models\Backend\ShippingType;
use App\Repositories\FrontWeb\Service\ServiceInterface;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    protected $repo;
    public function __construct(ServiceInterface $repo)
    {
        $this->repo = $repo;
    }
    public function index(){
        $services = $this->repo->insideService();
        return view('backend.front_web.service.inside_city',compact('services'));
    }
    public function outsideCity(){
        $services = $this->repo->outsideService();
        return view('backend.front_web.service.outside_city',compact('services'));
    }

    public function create(){
        return view('backend.front_web.service.create');
    }
    public function store(StoreRequest $request){
        if($this->repo->store($request)):
            Toastr::success(__('levels.service_added'),__('message.success'));
            return redirect()->route('service.index');
        else:
            Toastr::error(__('parcel.error_msg'),__('message.error'));
            return redirect()->back()->withInput();
        endif;
    }

    public function edit($id){
        $service  = $this->repo->getFind($id); 
        return view('backend.front_web.service.edit',compact('service'));
    }
    public function update(UpdateRequest $request,$id){
        if($this->repo->update($id,$request)):
            Toastr::success(__('levels.service_updated'),__('message.success'));
            return redirect()->route('service.index');
        else:
            Toastr::error(__('parcel.error_msg'),__('message.error'));
            return redirect()->back()->withInput();
        endif;
    }
    public function delete($id){
        if($this->repo->delete($id)):
            Toastr::success(__('levels.service_deleted'),__('message.success'));
            return redirect()->back();
        else:
            Toastr::error(__('parcel.error_msg'),__('message.error'));
            return redirect()->back();
        endif;
    }

    public function shippingtypes(Request $request){
    
        if(request()->ajax()):
            $shipping_types = ShippingType::where('delivery_type_id', $request->delivery_type_id)->get(); 
            if($request->delivery_type_id == 1):
                $dtype = 'inside';
            elseif($request->delivery_type_id == 3): 
                $dtype = 'outside';
            else:
                $dtype = '';
            endif;
            $options = '<option value="" selected>Select '.$dtype.' shipping type</option>'; 
            foreach ($shipping_types as $key => $shipping_type) {
                 $options .= '<option value="'.$shipping_type->id.'">'.$shipping_type->title.'</option>';
            }
    
            return response()->json([
                'shipping_types' => $options
            ]);
        endif;
        return '';
    }

}
