<?php

namespace App\Http\Controllers\Backend\HRM;

use App\Http\Controllers\Controller;
use App\Repositories\Attendance\AttendanceInterface;
use App\Repositories\User\UserInterface;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AttendanceController extends Controller
{
    protected $repo, $userRepo, $data = [];

    public function __construct(AttendanceInterface $repo, UserInterface $userRepo)
    {
        $this->repo     = $repo;
        $this->userRepo = $userRepo;
    }
    public function index(Request $request)
    {


        $data          = $this->repo->attendanceData();
        $attendances   = $this->repo->get();
        $users         = $this->userRepo->attendanceUser();

        return view('backend.hrm.attendance.index', compact('attendances', 'users', 'data', 'request'));
    }

    public function filter(Request $request)
    {
        $data          = $this->repo->attendanceData();
        $attendances   = $this->repo->get();
        $users         = $this->userRepo->getFilterAttendanceUsers($request);

        return view('backend.hrm.attendance.index', compact('attendances', 'users', 'data', 'request'));
    }

    public function store(Request $request)
    {

        $attendanceFind   = $this->repo->getFindDateWise($request->employee_id, $request->date);
        if ($attendanceFind) :
            Toastr::success(__('parcel.attendance_already_added_successfully'), __('message.success'));
            return redirect()->route('hrm.attendance.index');
        endif;
        if ($this->repo->store($request)) {
            Toastr::success(__('parcel.attendance_added_successfully'), __('message.success'));
            return redirect()->back();
        } else {
            Toastr::error(__('parcel.error_msg'), __('message.error'));
            return redirect()->back();
        }
    }



    public function update(Request $request)
    {

        if ($this->repo->update($request->id, $request)) {
            Toastr::success(__('parcel.attendance_updated_successfully'), __('message.success'));
            return redirect()->back();
        } else {
            Toastr::error(__('parcel.error_msg'), __('message.error'));
            return redirect()->back();
        }
    }


    public function destroy($id)
    {

        if ($this->repo->delete($id)) {
            Toastr::success(__('parcel.attendance_deleted_successfully'), __('message.success'));
            return redirect()->back();
        } else {
            Toastr::error(__('parcel.error_msg'), __('message.error'));
            return redirect()->back();
        }
    }



    public function createModal(Request $request)
    {
        $user = $this->userRepo->get($request->user_id);
        return view('backend.hrm.attendance.modal.create_modal', compact('user', 'request'));
    }


    public function edit(Request $request)
    {
        $attendance   = $this->repo->getFind($request->id);
        return view('backend.hrm.attendance.modal.edit_modal', compact('attendance'));
    }

    public function detailsModal(Request $request)
    {

        $attendance   = $this->repo->getFindDateWise($request->user_id, $request->date);
        return view('backend.hrm.attendance.modal.details_modal', compact('attendance'));
    }
    public function checkoutModal(Request $request)
    {
        $attendance   = $this->repo->getFindDateWise($request->user_id, $request->date);
        return view('backend.hrm.attendance.modal.checkout_modal', compact('attendance'));
    }

    public function checkinModal()
    {
        return view('backend.hrm.attendance.user_checkin_modal');
    }
    public function checkin()
    {

        $attendanceFind   = $this->repo->getFindDateWise(Auth::user()->id, date('Y-m-d'));
        if ($attendanceFind) :
            return response()->json([
                'already_attend'   => true,
                'message' => __('parcel.attendance_already_added_successfully')
            ], 200);
        endif;

        if ($this->repo->checkin()) {
            return response()->json([
                'success' => true,
                'message' => __('parcel.Check_in_successfully')
            ], 200);
        } else {
            return response()->json([
                'error'   => true,
                'message' => __('parcel.error_msg')
            ], 400);
        }
    }
    public function checkout()
    {

        $attendanceFind   = $this->repo->getFindDateWise(Auth::user()->id, date('Y-m-d'));
        if ($attendanceFind && $attendanceFind->check_out) :
            return response()->json([
                'already_attend'     => true,
                'message'            => __('parcel.attendance_already_checkout_successfully')
            ], 200);
        endif;

        if ($this->repo->checkout()) {
            return response()->json([
                'success' => true,
                'message' => __('parcel.Check_out_successfully')
            ], 200);
        } else {
            return response()->json([
                'error'   => true,
                'message' => __('parcel.error_msg')
            ], 400);
        }
    }


    public function reports(Request $request)
    {
        if($request->filter):
            Validator::make($request->all(),[
                'user_id' =>['required']
            ])->validate();
        endif;

        $this->data['request']       = $request;
        $this->data['users']         = $this->userRepo->adminUsers();
        if($request->user_id):
            $this->data['attendances']    = $this->repo->reports($request); 
        endif;
        return view('backend.hrm.attendance.attendance_reports', $this->data);
    }
}
