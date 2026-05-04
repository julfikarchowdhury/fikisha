<?php

namespace App\Repositories\LeaveType;

use App\Enums\Status;
use App\Models\Backend\HRM\LeaveType;
use App\Repositories\LeaveType\LeaveTypeInterface;

class LeaveTypeRepository implements LeaveTypeInterface
{

    protected $model;
    public function __construct(LeaveType $model)
    {
        $this->model  = $model;
    }
    public function get()
    {
        return $this->model::orderBy('position', 'asc')->paginate(10);
    }
    public function getActiveAll()
    {
        return $this->model::where(['status' => Status::ACTIVE])->orderBy('position', 'asc')->get();
    }
    public function getFind($id)
    {
        return $this->model::find($id);
    }
    public function store($request)
    {
        try {
            $leaveType           = new $this->model();
            $leaveType->name     = $request->name;
            $leaveType->position = $request->position;
            $leaveType->status   = $request->status;
            $leaveType->save();
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }
    public function update($id, $request)
    {
        try {
            $leaveType           = $this->model::find($id);
            $leaveType->name     = $request->name;
            $leaveType->position = $request->position;
            $leaveType->status   = $request->status;
            $leaveType->save();
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }
    public function delete($id)
    {
        return LeaveType::destroy($id);
    }
}
