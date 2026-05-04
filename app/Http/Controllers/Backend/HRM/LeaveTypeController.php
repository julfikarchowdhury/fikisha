<?php

namespace App\Http\Controllers\Backend\HRM;

use App\Http\Controllers\Controller;
use App\Http\Requests\LeaveType\StoreRequest;
use App\Repositories\LeaveType\LeaveTypeInterface;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;

class LeaveTypeController extends Controller
{

    protected $repo;
    public function __construct(LeaveTypeInterface $repo)
    {
        $this->repo = $repo;
    }
    public function index()
    {
        $leave_types = $this->repo->get();
        return view('backend.hrm.leave_type.index', compact('leave_types'));
    }

    public function create()
    {
        return view('backend.hrm.leave_type.create');
    }

    public function store(StoreRequest $request)
    {

        if ($this->repo->store($request)) {
            Toastr::success(__('parcel.leave_type_added_successfully'), __('message.success'));
            return redirect()->route('hrm.leave.type.index');
        } else {
            Toastr::error(__('parcel.error_msg'), __('message.error'));
            return redirect()->back();
        }
    }

    public function edit($id)
    {
        $leave_type = $this->repo->getFind($id);
        return view('backend.hrm.leave_type.edit', compact('leave_type'));
    }

    public function update(StoreRequest $request)
    {

        if ($this->repo->update($request->id, $request)) {
            Toastr::success(__('parcel.leave_type_updated_successfully'), __('message.success'));
            return redirect()->route('hrm.leave.type.index');
        } else {
            Toastr::error(__('parcel.error_msg'), __('message.error'));
            return redirect()->back();
        }
    }

    public function destroy($id)
    {

        if ($this->repo->delete($id)) {
            Toastr::success(__('parcel.leave_type_deleted_successfully'), __('message.success'));
            return redirect()->back();
        } else {
            Toastr::error(__('parcel.error_msg'), __('message.error'));
            return redirect()->back();
        }
    }
}
