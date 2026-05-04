<?php

namespace App\Repositories\Holiday;

use App\Enums\Status;
use App\Models\Backend\HRM\Holiday;
use Carbon\Carbon;

class HolidayRepository implements HolidayInterface
{
    protected $holidayModel;
    public function __construct(Holiday $holidayModel)
    {
        $this->holidayModel = $holidayModel;
    }
    public function get()
    {
        return $this->holidayModel::orderByDesc('id')->paginate(10);
    }

    public function getFind($id)
    {
        return $this->holidayModel::find($id);
    }
    public function store($request)
    {
        try {

            $holiday         = new $this->holidayModel();
            $holiday->name   = $request->name;
            $holiday->from   = $request->from_date;
            $holiday->to     = $request->to_date;
            $holiday->note   = $request->note;
            $holiday->save();
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }
    public function update($id, $request)
    {
        try {
            $holiday         = $this->holidayModel::find($id);
            $holiday->name   = $request->name;
            $holiday->from   =  $request->from_date;
            $holiday->to     =  $request->to_date;
            $holiday->note   = $request->note;
            $holiday->save();
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }
    public function delete($id)
    {
        try {
            $holiday   = $this->holidayModel->find($id);
            $holiday->delete();
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }
}
