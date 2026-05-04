<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Repositories\Zone\ZoneInterface;
use Illuminate\Http\Request;
use App\Http\Requests\Zone\StoreRequest;
use Brian2694\Toastr\Facades\Toastr;

class ZoneController extends Controller
{
    protected $repo;
    public function __construct(ZoneInterface $repo)
    {
        $this->repo    = $repo;
    }
   public function index  (Request $request){ 
        $zones = $this->repo->get(); 
        return view('backend.setting.zone.index',compact('zones'));
   }

   public function create  (){
       return view('backend.setting.zone.create');
   }

   public function store (StoreRequest $request){
        if($this->repo->store($request)):
            Toastr::success(__('parcel.zone_created_successfully'),__('message.success'));
            return redirect()->route('settings.zone.delivery-charge.index');
        else:
            Toastr::error(__('parcel.error_msg'),__('message.error'));
            return redirect()->back();
        endif;
   }

   public function edit  ($id){
        $zone = $this->repo->getFind($id);
        return view('backend.setting.zone.edit',compact('zone'));
   }
   public function update  (StoreRequest $request){
        if($this->repo->update($request)):
            Toastr::success(__('parcel.zone_updated_successfully'),__('message.success'));
            return redirect()->route('settings.zone.delivery-charge.index');
        else:
            Toastr::error(__('parcel.error_msg'),__('message.error'));
            return redirect()-back();
        endif;
   }
   public function delete ($id){
        if($this->repo->delete($id)):
            Toastr::success(__('parcel.zone_deleted_successfully'),__('message.success'));
            return redirect()->back();
        else:
            Toastr::error(__('parcel.error_msg'),__('message.error'));
            return redirect()-back();
        endif;
    }
}
