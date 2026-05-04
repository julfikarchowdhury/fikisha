<?php

namespace App\Http\Controllers\Backend\HRM;

use App\Enums\LeaveStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Leave\ApplyLeave\StoreRequest;
use App\Repositories\Leave\LeaveInterface;
use App\Repositories\LeaveAssign\LeaveAssignInterface;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApplyLeaveController extends Controller
{


    protected $repo,$leaveAssignRepo;
    public function __construct(LeaveInterface $repo,LeaveAssignInterface $leaveAssignRepo)
    {
        $this->repo            = $repo;
        $this->leaveAssignRepo = $leaveAssignRepo;
    }
    public function index(Request $request){
        $leaves          = $this->repo->myLeaves($request);
        $available_leavs = $this->leaveAssignRepo->getleaveAssignWithPaginate();
        return view('backend.hrm.leave.applyleave.index',compact('request','leaves','available_leavs'));
    }
    public function create(){
        return view('backend.hrm.leave.applyleave.create');
    }

    public function store(StoreRequest $request)
    { 
        $request['user_id'] = Auth::user()->id;
        $request['status']  = LeaveStatus::PENDING;
        $leaveAssign        = $this->leaveAssignRepo->getFind($request->leave_assign_id);
        if($leaveAssign && $leaveAssign->days >= $this->repo->availableLeave($request)):
            if($this->repo->store($request)){
                Toastr::success(__('parcel.leave_applied_successfully'),__('message.success'));
                return redirect()->route('hrm.apply.leave.index');
            }else{
                Toastr::error(__('parcel.error_msg'),__('message.error'));
                return redirect()->back()->withInput($request->all());
            } 
        elseif($leaveAssign == null):
            Toastr::error(__('parcel.error_msg'),__('message.error'));
            return redirect()->back()->withInput($request->all());
        elseif($this->repo->availableLeave($request)):
            Toastr::error(__('parcel.your_yearly_leave_facility_already_completed_or_requested_for_more_try_another_way'),__('errors'));
            return redirect()->back()->withInput($request->all());
        endif; 
    }
 
    public function destroy($id)
    { 
        $leave  = $this->repo->getFind($id);
        if(Auth::user()->id != $leave->user_id):
            Toastr::error(__('parcel.error_msg'),__('message.error'));
            return redirect()->back();
        endif;
        if($this->repo->delete($id)){
            Toastr::success(__('parcel.leave_deleted_successfully'),__('message.success'));
            return redirect()->back();
        }else{
            Toastr::error(__('parcel.error_msg'),__('message.error'));
            return redirect()->back();
        }
    }
  
}
