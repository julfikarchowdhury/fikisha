<?php
namespace App\Repositories\Leave;
interface LeaveInterface {
    public function get($request=null); 
    public function myLeaves($request=null); 
    public function availableLeave($request);
    public function getFind($id);
    public function store($request);
    public function delete($id);
    // public function leave_request_list();
    public function approval($request);
    public function reportsLeaves($request);
    public function totalAssignedLeaves($request);
}
