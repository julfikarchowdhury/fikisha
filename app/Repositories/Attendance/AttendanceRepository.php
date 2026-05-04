<?php

namespace App\Repositories\Attendance;

use App\Enums\AttendanceStatus;
use App\Models\Backend\HRM\Attendance;
use App\Models\Backend\HRM\DutySchedule;
use App\Models\User;
use App\Repositories\Attendance\AttendanceInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AttendanceRepository implements AttendanceInterface
{
    protected $attendanceModel;
    public function __construct(Attendance $attendanceModel)
    {
        $this->attendanceModel = $attendanceModel;
    }
    public function get()
    {
        return $this->attendanceModel::where(['user_id' => Auth::user()->id])->whereYear('date', Date('Y'))->orderByDesc('id')->paginate(15);
    }



    public function attendanceData()
    {
        $data = [];
        $data['monthyear']        = Carbon::now()->format('F Y');
        $data['start_month']      = Carbon::now()->startOfMonth()->toDateTimeString();
        $data['end_month']        = Carbon::now()->endOfMonth()->addSecond(1)->toDateTimeString();
        $data['total_month_days'] = Carbon::parse($data['start_month'])->diffInDays($data['end_month']);
        $data['full_month_dates'] = [];
        for ($i = 0; $i < $data['total_month_days']; ++$i) {
            $data['full_month_dates'][] =  Carbon::now()->startOfMonth()->addDay($i)->toDateString();
        }
        return $data;
    }

    public function getFind($id)
    {
        return $this->attendanceModel::find($id);
    }
    public function getFindDateWise($user_id, $date)
    {
        return  Attendance::where(['user_id' => $user_id])->whereDate('date', $date)->first();
    }



    public function store($request)
    {
        try {

            $user                           = User::find($request->user_id);
            $date                           = Carbon::parse($request->date)->format('Y-m-d');
            $attendance                     = new $this->attendanceModel();
            $attendance->user_id            = $user->id;
            $attendance->date               = $date;
            $attendance->check_in           = $request->check_in;
            $attendance->in_ip_address      = Request::ip();
            if ($request->check_out && !blank($request->check_out)) :
                $attendance->out_ip_address      = Request::ip();
                $attendance->check_out   = $request->check_out;
                $attendance->status      = AttendanceStatus::CHECK_OUT;
                //stay minutes 
                $stay_minutes = Carbon::parse($request->check_in)->diffInMinutes($request->check_out);
                $attendance->stay_time = $stay_minutes;

                //over stay time 
                $dutySchedule      = DutySchedule::where('role_id', $user->role_id)->whereYear('created_at', Carbon::parse($request->date)->year)->first();
                if ($dutySchedule) :
                    $schedule_minutes = Carbon::parse($dutySchedule->start_time)->diffInMinutes($dutySchedule->end_time);
                    if ($stay_minutes > $schedule_minutes) :
                        $over_stay_minutes  = ($stay_minutes - $schedule_minutes);
                        $attendance->over_stay_time = $over_stay_minutes;
                    endif;
                endif;
            //over stay time
            endif;
            $attendance->save();
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }
    public function update($id, $request)
    {
        try {

            $attendance                  = $this->attendanceModel::find($id);
            $attendance->check_in        = $request->check_in;
            if ($request->check_out) :
                $attendance->out_ip_address      = Request::ip();
                $attendance->check_out   = $request->check_out;
                $attendance->status      = AttendanceStatus::CHECK_OUT;
                //stay minutes 
                $stay_minutes            = Carbon::parse($request->check_in)->diffInMinutes($request->check_out);
                $attendance->stay_time   = $stay_minutes;

                //over stay time 
                $dutySchedule      = DutySchedule::where('role_id', $attendance->user->role_id)->whereYear('created_at', Carbon::parse($attendance->date)->year)->first();
                if ($dutySchedule) :
                    $schedule_minutes = Carbon::parse($dutySchedule->start_time)->diffInMinutes($dutySchedule->end_time);
                    if ($stay_minutes > $schedule_minutes) :
                        $over_stay_minutes  = ($stay_minutes - $schedule_minutes);
                        $attendance->over_stay_time = $over_stay_minutes;
                    endif;
                endif;
            //over stay time
            endif;
            $attendance->save();

            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }
    public function delete($id)
    {
        return $this->attendanceModel::destroy($id);
    }



    public function checkin()
    {
        try {

            $date                           = Carbon::now()->format('Y-m-d');
            $attendance                     = new $this->attendanceModel();
            $attendance->user_id            = Auth::user()->id;
            $attendance->date               = $date;
            $attendance->check_in           = date('h:i');
            $attendance->in_ip_address      = Request::ip();
            $attendance->save();
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function checkout()
    {
        try {

            $date                           = Carbon::now()->format('Y-m-d');
            $attendance                     = $this->attendanceModel::where(['user_id' => Auth::user()->id, 'date' => $date])->first();
            $checkout_time                  = date('h:i');
            if ($attendance) :
                $attendance->out_ip_address      = Request::ip();
                $attendance->check_out           = $checkout_time;
                $attendance->status              = AttendanceStatus::CHECK_OUT;
                //stay minutes 
                $stay_minutes = Carbon::parse($attendance->check_in)->diffInMinutes($checkout_time);
                $attendance->stay_time = $stay_minutes;

                //over stay time 
                $dutySchedule      = DutySchedule::where('role_id', $attendance->user->role_id)->whereYear('created_at', Carbon::parse($attendance->date)->year)->first();
                if ($dutySchedule) :
                    $schedule_minutes = Carbon::parse($dutySchedule->start_time)->diffInMinutes($dutySchedule->end_time);
                    if ($stay_minutes > $schedule_minutes) :
                        $over_stay_minutes  = ($stay_minutes - $schedule_minutes);
                        $attendance->over_stay_time = $over_stay_minutes;
                    endif;
                endif;
                //over stay time 

                $attendance->save();
                return true;
            endif;
            abort(400);
        } catch (\Throwable $th) {
            return false;
        }
    }



    public function reports($request)
    {
        return Attendance::where(function ($query) use ($request) {
            if ($request->date) :
                $date = explode('To', $request->date);
                if (is_array($date)):
                    $from   = Carbon::parse(trim($date[0]))->format('Y-m-d');
                    $to     = Carbon::parse(trim($date[1]))->format('Y-m-d');
                    $query->whereBetween('date', [$from, $to]);
                endif;
            endif;
            $query->where('user_id', $request->user_id);
        })->orderBy('date', 'ASC')->get();
    }
}
