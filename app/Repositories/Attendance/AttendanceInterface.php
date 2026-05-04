<?php
namespace App\Repositories\Attendance;
interface AttendanceInterface {
    public function get();  
    public function getFind($id);
    public function getFindDateWise($user_id,$date);
    public function store($request);
    public function update($id,$request);
    public function delete($id);  
    public function attendanceData();

    public function checkin();
    public function checkout();
    public function reports($request);
}
