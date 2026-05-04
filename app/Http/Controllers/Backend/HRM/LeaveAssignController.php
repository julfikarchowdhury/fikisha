<?php

namespace App\Http\Controllers\Backend\HRM;

use App\Http\Controllers\Controller;
use App\Http\Requests\LeaveAssign\StoreRequest;
use App\Models\Backend\HRM\LeaveAssign;
use App\Repositories\LeaveAssign\LeaveAssignInterface;
use App\Repositories\LeaveType\LeaveTypeInterface;
use App\Repositories\Role\RoleInterface;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;

class LeaveAssignController extends Controller
{
   
    protected $repo,$roleRepo,$LeaveTypeRepo;
    public function __construct(
            LeaveAssignInterface $repo,
            RoleInterface $roleRepo,
            LeaveTypeInterface $LeaveTypeRepo
        )
    {
        $this->repo          = $repo;
        $this->roleRepo      = $roleRepo;
        $this->LeaveTypeRepo = $LeaveTypeRepo;
    }
    public function index()
    {
        $leave_assigns   = $this->repo->get();
        return view('backend.hrm.leave_assign.index',compact('leave_assigns'));
    }
 
    public function create()
    {
        $roles       = $this->roleRepo->all();
        $leave_types = $this->LeaveTypeRepo->getActiveAll();
        return view('backend.hrm.leave_assign.create',compact('roles','leave_types'));
    }
 
    public function store(StoreRequest $request)
    {
        $check   = LeaveAssign::where(['role_id'=>$request->role_id,'type_id'=>$request->type_id])->whereYear('created_at',Date('Y'))->first();
        if($check):
            Toastr::error(__('parcel.leave_type_already_assigned'),__('message.error'));
            return redirect()->back()->withInput();
        endif;
        if($this->repo->store($request)){
            Toastr::success(__('parcel.leave_assign_added_successfully'),__('message.success'));
            return redirect()->route('hrm.leave.assign.index');
        }else{
            Toastr::error(__('parcel.error_msg'),__('message.error'));
            return redirect()->back();
        }
    }
 
    public function edit($id)
    {
        $roles         = $this->roleRepo->all();
        $leave_types   = $this->LeaveTypeRepo->getActiveAll();
        $leave_assign  = $this->repo->getFind($id);
        return view('backend.hrm.leave_assign.edit',compact('leave_assign','roles','leave_types'));
    }

    public function update(StoreRequest $request)
    {
         
        $findExistsAssign  = $this->repo->existsAssigned($request);
        if($findExistsAssign && $findExistsAssign->id != $request->id):
            Toastr::error(__('parcel.leave_type_already_assigned'),__('message.error'));
            return redirect()->back()->withInput();
        endif;
        if($this->repo->update($request->id,$request)){
            Toastr::success(__('parcel.leave_assign_updated_successfully'),__('message.success'));
            return redirect()->route('hrm.leave.assign.index');
        }else{
            Toastr::error(__('parcel.error_msg'),__('message.error'));
            return redirect()->back();
        }
    }

  
    public function destroy($id)
    {
        
        if($this->repo->delete($id)){
            Toastr::success(__('parcel.leave_assign_delete_successfully'),__('message.success'));
            return redirect()->back();
        }else{
            Toastr::error(__('parcel.error_msg'),__('message.error'));
            return redirect()->back();
        }
    }
 
}
