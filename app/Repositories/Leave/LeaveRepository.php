<?php

namespace App\Repositories\Leave;

use App\Enums\LeaveStatus;
use App\Enums\UserType;
use App\Models\Backend\HRM\LeaveAssign;
use App\Models\Backend\HRM\LeaveRequest;
use App\Models\Backend\Upload;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Repositories\Leave\LeaveInterface;
use App\Repositories\LeaveAssign\LeaveAssignInterface;

class LeaveRepository implements LeaveInterface
{
    protected $upload, $leaveRequestModel, $leaveAssignRepo;
    public function __construct(
        LeaveRequest $leaveRequestModel,
        LeaveAssignInterface $leaveAssignRepo
    ) {
        $this->leaveRequestModel = $leaveRequestModel;
        $this->leaveAssignRepo   = $leaveAssignRepo;
    }
    public function get($request = null)
    {
        if ($request && $request->filter):
            return $this->leaveRequestModel::where(function ($query) use ($request) {
                if ($request->user_id):
                    $query->where('user_id', $request->user_id);
                endif;
                if ($request->status):
                    $query->where('status', $request->status);
                endif;
                if ($request->date):
                    $date = explode('To', $request->date);
                    if (is_array($date)) {
                        $from   = Carbon::parse(trim($date[0]))->startOfDay()->toDateTimeString();
                        $to     = Carbon::parse(trim($date[1]))->endOfDay()->toDateTimeString();
                        $query->whereBetween('created_at', [$from, $to]);
                    }
                endif;
            })->orderByDesc('id')->paginate(10);
        else:
            return $this->leaveRequestModel::whereYear('created_at', Date('Y'))->orderByDesc('id')->paginate(10);
        endif;
    }
    public function myLeaves($request = null)
    { //only user panel showable
        if ($request && $request->filter):
            return $this->leaveRequestModel::where(function ($query) use ($request) {
                $query->where('user_id', Auth::user()->id);
                if ($request->status):
                    $query->where('status', $request->status);
                endif;
                if ($request->date):
                    $date = explode('To', $request->date);
                    if (is_array($date)) {
                        $from   = Carbon::parse(trim($date[0]))->startOfDay()->toDateTimeString();
                        $to     = Carbon::parse(trim($date[1]))->endOfDay()->toDateTimeString();
                        $query->whereBetween('created_at', [$from, $to]);
                    }
                endif;
            })->orderByDesc('id')->paginate(10);
        else:
            return $this->leaveRequestModel::where('user_id', Auth::user()->id)->whereYear('created_at', Date('Y'))->orderByDesc('id')->paginate(10);
        endif;
    }

    public function availableLeave($request)
    {
        try {
            $user           = User::find($request->user_id);
            $user_id    = $user->id;
            $leaveRequests = $this->leaveRequestModel::where([
                'user_id'    => $user_id,
                'leave_assign_id' => $request->leave_assign_id,
                'status'         => LeaveStatus::APPROVED
            ])->whereYear('created_at', Date('Y'))->get();
            $approvedDays  = 0;
            $requestDays   = 0;

            foreach ($leaveRequests as  $leave) {
                $start_time    = Carbon::parse($leave->leave_from)->startOfDay()->toDateTimeString();
                $end_time      = Carbon::parse($leave->leave_to)->endOfDay()->addMinute(1)->toDateTimeString();
                $approvedDays +=  Carbon::parse($start_time)->diff($end_time)->days;
            }

            $request_start_time    = Carbon::parse($request->leave_from)->startOfDay()->toDateTimeString();
            $request_end_time      = Carbon::parse($request->leave_to)->endOfDay()->addMinute(1)->toDateTimeString();
            $requestDays          += Carbon::parse($request_start_time)->diff($request_end_time)->days;
            return ($approvedDays + $requestDays);
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function getFind($id)
    {
        return $this->leaveRequestModel::find($id);
    }
    public function store($request)
    {
        try {

            $user        = User::find($request->user_id);
            $user_id     = $user->id;
            $role_id     = $user->role_id;

            $leave_assign                  = $this->leaveAssignRepo->getFind($request->leave_assign_id);
            $applyLeave                    = new $this->leaveRequestModel();
            $applyLeave->user_id           = $user_id;
            $applyLeave->role_id           = $role_id;
            $applyLeave->leave_assign_id   = $request->leave_assign_id;
            $applyLeave->type_id           = $leave_assign->type_id;
            $applyLeave->leave_from        = Carbon::parse($request->leave_from)->format('Y-m-d');
            $applyLeave->leave_to          = Carbon::parse($request->leave_to)->format('Y-m-d');
            if ($request->file):
                $applyLeave->file              = $this->file($request->file, '');
            endif;
            $applyLeave->reason            = $request->reason;
            $applyLeave->status            = $request->status;
            $applyLeave->save();
            return true;
        } catch (\Throwable $th) {

            return false;
        }
    }

    public function delete($id)
    {
        try {
            $applyLeave = $this->leaveRequestModel::find($id);
            if ($applyLeave->upload && file_exists(public_path($applyLeave->upload->original))):
                unlink(public_path($applyLeave->upload->original));
            endif;
            Upload::destroy($applyLeave->file);
            $applyLeave->delete();
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function approval($request)
    {
        try {
            $leave_request          = $this->leaveRequestModel::find($request->id);
            $leave_request->status  = $request->status;
            $leave_request->save();
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }


    public function file($image, $image_id = '')
    {
        try {
            $image_name = '';
            if (!blank($image)) {
                $destinationPath       = public_path('uploads/leave');
                $profileImage          = date('YmdHis') . "." . $image->getClientOriginalExtension();
                $image->move($destinationPath, $profileImage);
                $image_name            = 'uploads/leave/' . $profileImage;
            }
            if (blank($image_id)) {
                $upload           = new Upload();
            } else {
                $upload           = Upload::find($image_id);
                if (file_exists(public_path($upload->original))) {
                    unlink(public_path($upload->original));
                }
            }
            $upload->original     = $image_name;
            $upload->save();
            return $upload->id;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function reportsLeaves($request)
    {
        return LeaveRequest::where(function ($query) use ($request) {
            if ($request->date) :
                $date = explode('To', $request->date);
                if (is_array($date)):
                    $from   = Carbon::parse(trim($date[0]))->format('Y-m-d');
                    $to     = Carbon::parse(trim($date[1]))->format('Y-m-d');
                    $query->whereBetween('leave_from', [$from, $to]);
                    $query->orWhereBetween('leave_to', [$from, $to]);
                endif;
            endif;
        })->where('user_id', $request->user_id)->orderBy('created_at', 'ASC')->get();
    }


    public function totalAssignedLeaves($request)
    {
        $user   = User::find($request->user_id);
        return LeaveAssign::where(function ($query) use ($request) {
            if ($request->date) :
                $date = explode('To', $request->date);
                if (is_array($date)):
                    $from   = Carbon::parse(trim($date[0]))->format('Y-m-d');
                    $to     = Carbon::parse(trim($date[1]))->format('Y-m-d');
                    $query->whereBetween('created_at', [$from, $to]);
                endif;
            endif;
        })->where('role_id', $user->role_id)->orderBy('id', 'ASC')->get();
    }
}
