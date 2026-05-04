<?php

namespace App\Http\Controllers\Backend\HRM;

use App\Http\Controllers\Controller;
use App\Http\Requests\DutySchedule\StoreRequest;
use App\Http\Requests\DutySchedule\UpdateRequest;
use App\Repositories\DutySchedule\DutyScheduleInterface;
use App\Repositories\Role\RoleInterface;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;

class DutyScheduleController extends Controller
{
    protected $repo,$roleRepo;
    public function __construct(DutyScheduleInterface $repo,RoleInterface $roleRepo){
        $this->repo     = $repo;
        $this->roleRepo = $roleRepo;
    }

    public function index()
    {  
        $duty_schedules    = $this->repo->get();
        return view('backend.hrm.duty_schedule.index',compact('duty_schedules'));
    } 
    
    public function create()
    {
        $roles = $this->roleRepo->getRole();
        return view('backend.hrm.duty_schedule.create',compact('roles'));
    }

    public function store(StoreRequest $request)
    {
       
        if($this->repo->store($request)){
            Toastr::success(__('parcel.duty_schedule_added_successfully'),__('message.success'));
            return redirect()->route('hrm.duty.schedule.index');
        }else{
            Toastr::error(__('parcel.error_msg'),__('message.error'));
            return redirect()->back();
        }
    }


    public function edit($id)
    {
        $duty_schedule = $this->repo->getFind($id);
        $roles         = $this->roleRepo->getRole();
        return view('backend.hrm.duty_schedule.edit',compact('duty_schedule','roles'));
    }

    public function update(UpdateRequest $request)
    {
        
        if($this->repo->update($request->id,$request)){
            Toastr::success(__('parcel.duty_schedule_updated_successfully'),__('message.success'));
            return redirect()->route('hrm.duty.schedule.index');
        }else{
            Toastr::error(__('parcel.error_msg'),__('message.error'));
            return redirect()->back();
        }
    }

    public function destroy($id)
    {
        
        if($this->repo->delete($id)){
            Toastr::success(__('parcel.duty_schedule_deleted_successfully'),__('message.success'));
            return redirect()->back();
        }else{
            Toastr::error(__('parcel.error_msg'),__('message.error'));
            return redirect()->back();
        }
    }

 
}
