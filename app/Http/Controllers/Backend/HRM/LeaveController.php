<?php

namespace App\Http\Controllers\Backend\HRM;

use App\Enums\LeaveStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Leave\StoreRequest;
use App\Repositories\Leave\LeaveInterface;
use App\Repositories\LeaveAssign\LeaveAssignInterface;
use App\Repositories\User\UserInterface;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LeaveController extends Controller
{
    protected $repo,$leaveAssignRepo,$userRepo,$data=[];
    public function __construct(
            LeaveInterface $repo,
            LeaveAssignInterface $leaveAssignRepo,
            UserInterface   $userRepo
        )
    {
        $this->repo            = $repo;
        $this->leaveAssignRepo = $leaveAssignRepo;
        $this->userRepo        = $userRepo;
    }
    public function index(Request $request)
    {  
        
        $leaves        = $this->repo->get($request); 
        $users         =  $this->userRepo->LeaveApplyUser();
        return view('backend.hrm.leave.index',compact('request','leaves','users'));
    }
   
    public function create()
    { 
        $users         =  $this->userRepo->LeaveApplyUser();
        return view('backend.hrm.leave.create',compact('users'));
    }

    public function AssignedLeave(Request $request){
        if($request->ajax()):
            $assigned_leaves     = $this->leaveAssignRepo->AssignedLeave($request->user_id);
            $options = '';
            if (!blank($assigned_leaves)):
                foreach ($assigned_leaves as $leave):
                    $options .= '<option value="'.$leave->id.'">'.$leave->leaveType->name.'</option>';
                endforeach;
            endif;
            return $options;
        endif;
        return '';
    }


    public function store(StoreRequest $request)
    { 
        $leaveAssign   = $this->leaveAssignRepo->getFind($request->leave_assign_id);
        if($leaveAssign && $leaveAssign->days >= $this->repo->availableLeave($request)):
            if($this->repo->store($request)){
                Toastr::success(__('parcel.leave_applied_successfully'),__('message.success'));
                return redirect()->route('hrm.leave.index');
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
        if($this->repo->delete($id)){
            Toastr::success(__('parcel.leave_deleted_successfully'),__('message.success'));
            return redirect()->back();
        }else{
            Toastr::error(__('parcel.error_msg'),__('message.error'));
            return redirect()->back();
        }
    }
  
    public function approval(Request $request){
 
        if($this->repo->approval($request)):
            if($request->status == LeaveStatus::APPROVED):
                Toastr::success(__('parcel.leave_approved_successfully'),__('message.success'));
                return redirect()->back();
            elseif($request->status == LeaveStatus::REJECTED):
                Toastr::success(__('parcel.leave_rejected_successfully'),__('message.success'));
                return redirect()->back();
            endif;
        else:
            Toastr::error(__('parcel.error_msg'),__('message.error'));
            return redirect()->back();
        endif;
    }

    public function reports(Request $request){
 
        if($request->filter):
            Validator::make($request->all(),[
                'user_id' =>['required']
            ])->validate();
        endif;

        $this->data['request']   = $request;
        $this->data['users']     = $this->userRepo->adminUsers();
        if($request->user_id):
            $this->data['total_assigned_leaves']   = $this->repo->totalAssignedLeaves($request)->sum('days');
            $this->data['leaves']                  = $this->repo->reportsLeaves($request);
            $this->data['total_approved_leaves']   = $this->repo->reportsLeaves($request)->where('status',LeaveStatus::APPROVED)->sum('total_days');
            $this->data['total_pending_leaves']    = $this->repo->reportsLeaves($request)->where('status',LeaveStatus::PENDING)->sum('total_days');
            $this->data['total_rejected_leaves']   = $this->repo->reportsLeaves($request)->where('status',LeaveStatus::REJECTED)->sum('total_days');
        endif;
        return view('backend.hrm.leave.leave_reports',$this->data);
    }

    
}
