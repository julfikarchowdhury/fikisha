<?php

namespace App\Repositories\DutySchedule;

use App\Models\Backend\HRM\DutySchedule;
use App\Repositories\DutySchedule\DutyScheduleInterface;

class DutyScheduleRepository  implements DutyScheduleInterface
{
    public function get()
    {
        return DutySchedule::orderByDesc('id')->whereYear('created_at', date('Y'))->paginate(10);
    }

    public function getFind($id)
    {
        return DutySchedule::find($id);
    }
    public function store($request)
    {
        try {
            $dutySchedule             = new DutySchedule();
            $dutySchedule->role_id    = $request->role_id;
            $dutySchedule->start_time = $request->start_time;
            $dutySchedule->end_time   = $request->end_time;
            $dutySchedule->save();
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }
    public function update($id, $request)
    {
        try {
            $dutySchedule             = DutySchedule::find($id);
            $dutySchedule->role_id    = $request->role_id;
            $dutySchedule->start_time = $request->start_time;
            $dutySchedule->end_time   = $request->end_time;
            $dutySchedule->save();
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }
    public function delete($id)
    {
        return DutySchedule::destroy($id);
    }
}
