<?php

namespace App\Http\Controllers\Backend\HRM;

use App\Http\Controllers\Controller;
use App\Http\Requests\Holiday\StoreRequest;
use App\Repositories\Holiday\HolidayInterface;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request; 

class HolidayController extends Controller
{
    protected $repo;
    public function __construct(HolidayInterface $repo)
    {
        $this->repo = $repo;
    }
    public function index()
    {
        $holidays    = $this->repo->get();
        return view('backend.hrm.holiday.index',compact('holidays'));
    }
 

    public function create()
    {
        return view('backend.hrm.holiday.create');
    }
 
    public function store(StoreRequest $request)
    {
        
        if($this->repo->store($request)){
            Toastr::success(__('parcel.holiday_added_successfully'),__('message.success'));
            return redirect()->route('hrm.holiday.index');
        }else{
            Toastr::error(__('parcel.error_msg'),__('message.error'));
            return redirect()->back();
        }
    }


    public function edit($id)
    {
        $holiday     = $this->repo->getFind($id);
        return view('backend.hrm.holiday.edit',compact('holiday'));
    }

    public function update(StoreRequest $request)
    {
       
        if($this->repo->update($request->id,$request)){
            Toastr::success(__('parcel.holiday_updated_successfully'),__('message.success'));
            return redirect()->route('hrm.holiday.index');
        }else{
            Toastr::error(__('parcel.error_msg'),__('message.error'));
            return redirect()->back();
        }
    }

    public function destroy($id)
    {
       
        if($this->repo->delete($id)){
            Toastr::success(__('parcel.holiday_deleted_successfully'),__('message.success'));
            return redirect()->back();
        }else{
            Toastr::error(__('parcel.error_msg'),__('message.error'));
            return redirect()->back();
        }
    }

  
}
