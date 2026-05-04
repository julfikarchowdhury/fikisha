<?php
namespace App\Repositories\LeaveAssign;
interface LeaveAssignInterface {
    public function get(); 
    public function leaveAssign($request);
    public function AssignedLeave($user_id);
    public function leaveAssignWithPaginate();
    public function getleaveAssignWithPaginate();
    public function getFind($id);
    public function store($request);
    public function update($id,$request);
    public function delete($id);
    public function existsAssigned($request);
}
