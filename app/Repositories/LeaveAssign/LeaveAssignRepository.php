<?php

namespace App\Repositories\LeaveAssign;

use App\Enums\Status;
use App\Enums\UserType;
use App\Models\Backend\HRM\LeaveAssign;
use App\Models\User;
use App\Repositories\LeaveAssign\LeaveAssignInterface;
use Illuminate\Support\Facades\Auth;

class LeaveAssignRepository implements LeaveAssignInterface
{
    protected $leaveAssignModel;
    public function __construct(LeaveAssign $leaveAssignModel)
    {
        $this->leaveAssignModel = $leaveAssignModel;
    }
    public function get()
    {
        return $this->leaveAssignModel::whereYear('created_at', Date('Y'))->orderByDesc('id')->paginate(10);
    }

    public function existsAssigned($request)
    {
        return $this->leaveAssignModel::where(['role_id' => $request->role_id, 'type_id' => $request->type_id])->whereYear('created_at', Date('Y'))->first();
    }

    public function leaveAssign($request)
    {
        return $this->leaveAssignModel::where(['role_id' => $request->role_id, 'status' => Status::ACTIVE])->whereYear('created_at', Date('Y'))->get();
    }
    public function AssignedLeave($user_id)
    {
        $user = User::find($user_id);
        return $this->leaveAssignModel::where(['role_id' => $user->role_id, 'status' => Status::ACTIVE])->whereYear('created_at', Date('Y'))->get();
    }

    public function leaveAssignWithPaginate()
    {
        return $this->leaveAssignModel::where(['role_id' => Auth::user()->role_id, 'status' => Status::ACTIVE])->whereYear('created_at', Date('Y'))->paginate(10);
    }
    public function getleaveAssignWithPaginate()
    {
        return $this->leaveAssignModel::where(['role_id' => Auth::user()->role_id, 'status' => Status::ACTIVE])->whereYear('created_at', Date('Y'))->paginate(5);
    }

    public function getFind($id)
    {
        return $this->leaveAssignModel::find($id);
    }
    public function store($request)
    {
        try {
            $leaveAssign            = new $this->leaveAssignModel();
            $leaveAssign->role_id   = $request->role_id;
            $leaveAssign->type_id   = $request->type_id;
            $leaveAssign->days      = $request->days;
            $leaveAssign->status    = $request->status;
            $leaveAssign->save();
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }
    public function update($id, $request)
    {
        try {
            $leaveAssign            = $this->leaveAssignModel::find($id);
            $leaveAssign->role_id   = $request->role_id;
            $leaveAssign->type_id   = $request->type_id;
            $leaveAssign->days      = $request->days;
            $leaveAssign->status    = $request->status;
            $leaveAssign->save();
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }
    public function delete($id)
    {
        return $this->leaveAssignModel::destroy($id);
    }
}
